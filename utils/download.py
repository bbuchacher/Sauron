import requests
import MySQLdb
import MySQLdb.cursors
import logging
import logging
from BeautifulSoup import BeautifulSoup

logging.basicConfig(format='%(asctime)s %(levelname)s:%(message)s',datefmt = '%s',filename='/var/log/crit_io/download.log',level=logging.DEBUG )

# Mysql Database Information
MYSQLDBHOST = "localhost"
MYSQLUSER = "root"
MYSQLPASSWORD = ""

database_connection = MySQLdb.connect(MYSQLDBHOST, MYSQLUSER, MYSQLPASSWORD, 'critical_io', cursorclass=MySQLdb.cursors.DictCursor);

session = requests.session()
r = session.get('https://scans.io/study/sonar.cio')

soup = BeautifulSoup(r.text)
table = soup.find('table')

rows = table.findAll('tr')
for row in rows:
	i = row.findAll('td')
	raw_sha1hash = i[2]
	raw_fileinfo = i[0]
	shahash = str(raw_sha1hash).split('>')[2].strip('</code')
	url = str(raw_fileinfo).split('"')[1]
	filename = str(raw_fileinfo).split('>')[2].strip('</a')
	sql = database_connection.cursor()
	query = 'SELECT hash from files where hash = "' + shahash + '"'
	sql.execute(query)
	result = sql.fetchall()
	sql.close
	if result:
		logging.debug("File: " + filename + " is already in queue... Skipping.... ")
	else:
		status = "downloading"
		sql.execute("INSERT INTO files(filename,hash,status) VALUES(%s, %s, %s);", (filename,shahash,status))
		database_connection.commit()
		sql.close()
		logging.debug("File: " + filename + " found with URL: " + url + " and hash: " + shahash + "Downloading....")
		r = session.get(url, stream=True)
		path = "/storage/crit_io/" + filename
		with open(path, 'wb') as f:
				for chunk in r.iter_content(chunk_size=1024):
					if chunk:
						f.write(chunk)
						f.flush()
		sql = database_connection.cursor()
		query = 'UPDATE files SET status = "processed" where hash = "' + shahash + '"'
		sql.execute(query)
		database_connection.commit()
		sql.close
		logging.debug("Download Done")

import MySQLdb
import MySQLdb.cursors
import hashlib


# Mysql Database Information
MYSQLDBHOST = "localhost"
MYSQLUSER = "root"
MYSQLPASSWORD = ""

database_connection = MySQLdb.connect(MYSQLDBHOST, MYSQLUSER, MYSQLPASSWORD, '', cursorclass=MySQLdb.cursors.DictCursor);



def initdb():
	sql = database_connection.cursor()
	sql.execute('CREATE DATABASE IF NOT EXISTS critical_io')
	sql.execute('use critical_io')
	
	sql.execute("CREATE TABLE IF NOT EXISTS \
		files(id INT(255) AUTO_INCREMENT, filename VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, status VARCHAR(255), primary key (id))")
	
	
	database_connection.commit()
	sql.close()


initdb()

print "DONE"


import MySQLdb
import MySQLdb.cursors
import hashlib
import random


# Mysql Database Information
MYSQLDBHOST = "localhost"
MYSQLUSER = "root"
MYSQLPASSWORD = "password"

# Sauron Default Users
DEFAULT_ADMIN = 'admin'
DEFAULT_PASSWORD = 'password'
DEFAULT_EMAIL = 'bcbuchacher@gmail.com'
#There is roles built into the webapp however currently not in use. 
DEFAULT_ADMIN_ROLE = 'superadmin'

SYSTEM_SECRET = 'DemoSaltGoesHere'
# Would do salt and api_key differantly if expected massive ammount of users, would ensure that key didnt exist before creation, etc. 
SALT = ''.join(random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') for i in range(10))
API_KEY = ''.join(random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') for i in range(32))

database_connection = MySQLdb.connect(MYSQLDBHOST, MYSQLUSER, MYSQLPASSWORD, '', cursorclass=MySQLdb.cursors.DictCursor);



def initdb(DEFAULT_ADMIN,HASHED_PASS,DEFAULT_EMAIL,DEFAULT_ADMIN_ROLE):
	sql = database_connection.cursor()
	sql.execute('CREATE DATABASE IF NOT EXISTS Sauron')
	sql.execute('use Sauron')
	
	sql.execute("CREATE TABLE IF NOT EXISTS \
		users(id INT(255) AUTO_INCREMENT, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, primary key (id))")
	
	sql.execute("INSERT INTO users(username,password,salt,email,role,api_key) VALUES(%s, %s, %s, %s, %s,%s);", (DEFAULT_ADMIN,HASHED_PASS,SALT,DEFAULT_EMAIL,DEFAULT_ADMIN_ROLE,API_KEY))
	database_connection.commit()
	sql.close()

SALTED_PASSWORD = SYSTEM_SECRET + SALT + DEFAULT_PASSWORD

HASHED_PASS = hashlib.md5(SALTED_PASSWORD).hexdigest()

initdb(DEFAULT_ADMIN,HASHED_PASS,DEFAULT_EMAIL,DEFAULT_ADMIN_ROLE)

print "DONE"

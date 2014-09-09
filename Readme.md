Sauron
==============

About
--------------
This was a project for an interview I did and part of the project was to have the code available on github. If this is usefull to you then awesome, if you use it and find a bug submit a pull request and even better. 
This app will download and parse the logs from Rapid7's Internet-Wide Scan Data Repository and input them into Elastic Search or MySQL. 

The web interface will provide a search and api function. 

...More Coming very soon. 


Setup
--------------
Required Python modules are ElasticSearch, Requests, MySQLdb

Once required modules are installed

run python utils/initdb.py

Copy all files in web_src/ to your web root. ie /var/www/html/

Edit the search.php file to contain your ip address of elasticsearch nodes. 

Edit the lib/config.php file to contain your MySQL database settings. 

All files are set to download to /storage/crit_io

mkdir -p /storage/crit_io

Once paths are created and db is initilized. 

python utils/initfiledb.py

Done!

Run the download and parse scripts one time initially then schedule cron to run them daily after they complete.

python utils/download.py
python utils/parse-threaded.py




API Usage
--------------

ex:
curl -XPOST '192.168.1.6/search.php' -d 'api=1&api_key=BJ8G78E6HBYJZ0Z3WW58GCTY9UU56WTO&query=USA'

The Default query will look and match in all fields.

Required Fields are: 
api=1
api_key = YourApiKey
query = "What you are searching for"

Available Fields for the search to look through are: 
  
  host_ip

  rapid7_hash
  
  service_port
  
  service_protocol
  
  service_banner
  
  host_country
  
  host_ip_geolocation
  
  service_type
  
ex:
This will only match the query against the host_country field, you may add as many as you like. 
curl -XPOST '192.168.1.6/search.php' -d 'api=1&api_key=BJ8G78E6HBYJZ0Z3WW58GCTY9UU56WTO&host_country&query=USA'

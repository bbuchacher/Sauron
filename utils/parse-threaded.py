import Queue
import threading
import json
import random
import MySQLdb
import MySQLdb.cursors
import subprocess
import os.path
from datetime import datetime
from elasticsearch import Elasticsearch
from elasticsearch import helpers
import logging 

logging.basicConfig(format='%(asctime)s %(levelname)s:%(message)s',datefmt = '%s',filename='/var/log/crit_io/parse.log',level=logging.DEBUG )

to_process = []

# Mysql Database Information
MYSQLDBHOST = "localhost"
MYSQLUSER = "root"
MYSQLPASSWORD = ""

database_connection = MySQLdb.connect(MYSQLDBHOST, MYSQLUSER, MYSQLPASSWORD, 'critical_io', cursorclass=MySQLdb.cursors.DictCursor);
 
queue = Queue.Queue()

class ThreadJob(threading.Thread):
  def __init__(self, queue):
    threading.Thread.__init__(self)
    self.queue = queue 

  def run(self):
    while True:
      current_file = self.queue.get()
      command = "/usr/bin/bunzip2 " + current_file
      print command
      file_check = os.path.isfile(current_file)
      if file_check == True:

        subprocess.call(command, shell=True)
        bunziped_file = current_file.strip(".bz2")
        raw_data=open(bunziped_file)
        scan_data = []
        
        for json_record in raw_data:
          try:
            parsed_record = json.loads(str(json_record))
          except Exception as E:
            print filelog
            print json_record
            print E
            pass
          
          # It looks ugly but Handles any missing keys or values 
          try:
            try: 
              ip = parsed_record['_id']['ip']
            except TypeError:
              pass
            try:
              rapid7_hash = parsed_record['_id']['h']
            except TypeError:
              pass
            try:
              port = parsed_record['port']
            except TypeError:
              pass
            try:
              proto = parsed_record['proto']
            except TypeError:
              pass
            try:
              banner = parsed_record['banner']
            except TypeError:
              pass
            try:
              country = parsed_record['geo']['c']
            except TypeError:
              pass
            try:
              geoloc = parsed_record['geo']['loc']
            except TypeError:
              pass
            try:
              svc_name = parsed_record['name']
            except TypeError:
              pass
            try:
              scan_time = parsed_record['t']['$date']
            except TypeError:
              pass
          except KeyError:
            pass
          except Exception as Err: 
            print Err
            break

          data = {
          "_index": "critical_io",
          "_type": "scan_data",
            "_id" : ''.join(random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') for i in range(64)),
          "@timestamp" : datetime.utcfromtimestamp(float(scan_time)/1000.).isoformat(),
          "host_ip" : ip,
          "rapid7_hash" : rapid7_hash,
          "service_port" : port,
          "service_protocol" : proto,
          "service_banner" : banner,
          "host_country" : country,
          "host_ip_geolocation" : geoloc,
          "service_type" : svc_name
          }
          scan_data.append(data) 

          es = Elasticsearch(['192.168.1.13:9200', '192.168.1.7:9200', '192.168.1.4:9200', '192.168.1.9:9200'])
          if (len(scan_data) % 10000 == 0):
            helpers.bulk(es, scan_data,timeout=120)
            scan_data = []
        if len(scan_data) > 0:
          helpers.bulk(es, scan_data)

        raw_data.close()

      else:
        print "No File Found Skipping.."
        self.queue.task_done()
               
def get_files():
  sql = database_connection.cursor()
  query = "SELECT filename from files where status = 'processed'"
  sql.execute(query)
  results = sql.fetchall()

  for i in results:
    query = 'UPDATE files SET status ="indexing" WHERE filename = "' + i['filename'] +'"'
    sql.execute(query)
    database_connection.commit()
    path = "/storage/crit_io/" + i['filename']
    to_process.append(path)

  sql.close



def main():
  get_files()
  spawn_threads(to_process)


def spawn_threads(to_process):     
  for i in range(5):
    t = ThreadJob(queue)
    t.setDaemon(True)
    t.start()

  for filelog in to_process:
    queue.put(filelog)
  
  queue.join()
 
main()


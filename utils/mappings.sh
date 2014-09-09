#!/bin/bash 
#run on a master node. 
curl -XPUT http://localhost:9200/_template/logstash_per_index -d '
{
    "template" : "critical_io*",
    "mappings" : {
      "scan_data" : {
         "properties": {
            "@timestamp":{"type":"date",
            "format":"dateOptionalTime"},
            "@version":{"type":"string"},
            "host_ip":{"type":"string"},
            "service_port":{"type":"string"},
            "host_country":{"type":"string"},
            "service_name":{"type":"string"},
            "service_protocol":{"type":"string"},
            "service_banner":{"type":"string"},
            "host_geolocation":{"type":"string"},
            "service_type":{"type":"string"},
            "rapid7_hash":{"type":"string"}
            
      }
    }
  }
}'

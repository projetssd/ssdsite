#!/bin/bash
rm -rf /var/www/seedboxdocker.website/jsons/
mkdir -p /var/www/seedboxdocker.website/jsons/
for CONTAINER in $(docker container ls --format="{{.Names}}" --all); do docker inspect ${CONTAINER} | jq '.[] | {'$CONTAINER': .State}' > /var/www/seedboxdocker.website/jsons/${CONTAINER}.json; done

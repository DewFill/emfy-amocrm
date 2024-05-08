#!/bin/bash

# Generate API token from DNS web console
API_TOKEN="b9ee43df7130dc0f44eab513675f890443ea492ded5b314ccd12d024a1163ef2"

# Create challenge TXT record
curl "http://dns:5380/api/zones/records/add?token=$API_TOKEN&domain=_acme-challenge.$CERTBOT_DOMAIN&type=TXT&ttl=60&text=$CERTBOT_VALIDATION"

# Sleep to make sure the change has time to propagate from primary to secondary name servers
sleep 25
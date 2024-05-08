#!/bin/bash

# Generate API token from DNS web console
API_TOKEN="b9ee43df7130dc0f44eab513675f890443ea492ded5b314ccd12d024a1163ef2"

# Delete challenge TXT record
curl "http://dns:5380/api/zones/records/delete?token=$API_TOKEN&domain=_acme-challenge.$CERTBOT_DOMAIN&type=TXT&text=$CERTBOT_VALIDATION"
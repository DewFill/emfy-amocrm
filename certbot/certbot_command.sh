#!/bin/bash

CERT_PATH="/etc/letsencrypt/live/artemy.net/cert.pem"

if [ ! -f "$CERT_PATH" ]; then
    echo "Certificate file not found. Running Certbot..."
    certbot certonly \
        --manual \
        --preferred-challenges=dns \
        --manual-auth-hook /var/www/html/certbot/certbot-auth.sh \
        --manual-cleanup-hook /var/www/html/certbot/certbot-cleanup.sh \
        -d artemy.net \
        -d "*.artemy.net"
else
    echo "Certificate file already exists. Skipping Certbot."
fi
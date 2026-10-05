#!/bin/sh
# Genera un certificado autofirmado para la fase HTTPS del laboratorio.
# Uso: ./generate-cert.sh [dias_validez] [CN]
set -e

DAYS="${1:-365}"
CN="${2:-notes.local}"
DIR="$(cd "$(dirname "$0")" && pwd)"

openssl req -x509 -nodes -newkey rsa:2048 \
  -keyout "$DIR/selfsigned.key" \
  -out "$DIR/selfsigned.crt" \
  -days "$DAYS" \
  -subj "/C=BO/ST=La Paz/L=La Paz/O=Lab Forense/CN=$CN"

echo "Certificado generado:"
echo "  $DIR/selfsigned.crt"
echo "  $DIR/selfsigned.key"

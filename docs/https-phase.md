# Fase 2: HTTPS con certificado autofirmado

Esta fase reutiliza la misma app y el mismo escenario de 3 máquinas de
[`forensic-lab.md`](forensic-lab.md), pero sirviendo por HTTPS para
comparar qué ve ahora el "atacante".

## 1. Generar el certificado autofirmado

En la máquina "Servidor":

```bash
cd notes-app
./nginx/certs/generate-cert.sh
```

Esto crea `nginx/certs/selfsigned.crt` y `nginx/certs/selfsigned.key`,
válidos por 365 días para el CN `notes.local` (puedes pasar otro CN
como segundo argumento del script).

## 2. Activar el virtual host HTTPS

```bash
mv nginx/conf.d/default-ssl.conf.disabled nginx/conf.d/default-ssl.conf
docker compose restart nginx
```

Ahora nginx escucha también en el puerto 443.

## 3. Repetir la captura desde el atacante

Con Wireshark/tcpdump corriendo igual que en la Fase 1, el cliente
ahora visita:

```
https://192.168.50.10/login.php
```

El navegador mostrará una advertencia de certificado no confiable
(normal: es autofirmado) — acéptala para continuar con el laboratorio.

## 4. Analizar la captura cifrada

En Wireshark, filtra por `tls.handshake.type == 1` para ver el
*Client Hello* y confirma que, a diferencia de la Fase 1:
- Ya no existe un paquete `POST /login.php` legible en claro.
- El flujo aparece como **TLS Application Data**: contenido cifrado,
  sin usuario ni contraseña visibles al hacer "Follow → TLS Stream"
  (sin la clave privada del servidor, Wireshark no puede descifrarlo).

## 5. Evidencia para el informe

Documenta, en contraste con la Fase 1:
- Captura del *handshake* TLS (Client Hello / Server Hello / certificado).
- Captura mostrando que el stream de la petición de login aparece como
  "Application Data" cifrada, sin credenciales legibles.
- Una conclusión breve comparando ambos escenarios.

## Nota sobre certificados autofirmados

En producción se usaría un certificado emitido por una CA reconocida
(ej. Let's Encrypt) para evitar la advertencia del navegador y validar
la identidad del servidor. El autofirmado es suficiente para demostrar
el cifrado del canal en un laboratorio, pero no certifica identidad.

# Laboratorio: captura de credenciales en HTTP (red local)

Este documento describe el escenario de **3 máquinas** para demostrar,
en un entorno controlado, por qué HTTP sin cifrar expone credenciales.

## Topología

```
            Red local / VLAN de laboratorio (ej. 192.168.50.0/24)
   ┌───────────────┐      ┌───────────────┐      ┌───────────────┐
   │   Servidor     │      │    Cliente     │      │   Atacante     │
   │ 192.168.50.10  │      │ 192.168.50.20  │      │ 192.168.50.30  │
   │ docker compose │      │ navegador web  │      │ Wireshark /    │
   │ (nginx+php+db) │      │                │      │ tcpdump        │
   └───────────────┘      └───────────────┘      └───────────────┘
            └──────────────────────┴──────────────────────┘
                         mismo segmento de red
```

Las tres máquinas pueden ser VMs (VirtualBox/VMware) en una red
"Host-only" o "Internal Network", o contenedores/VMs en una VLAN
aislada. Lo importante es que compartan el mismo segmento de capa 2
para que el tráfico sea visible (en un switch real con puertos
aislados necesitarías *port mirroring*/SPAN o un hub; en una red
virtual de laboratorio basta con estar en la misma subred y usar modo
promiscuo).

## 1. Preparar el servidor

En la máquina "Servidor":

```bash
cd notes-app
docker compose up -d --build
```

Verifica que escucha en el puerto 80:

```bash
curl -I http://localhost/
```

## 2. Preparar el atacante

En la máquina "Atacante", instala Wireshark o tcpdump y empieza a
capturar **antes** de que el cliente inicie sesión:

**Con Wireshark (GUI):**
1. Selecciona la interfaz de red del segmento de laboratorio.
2. Aplica el filtro: `http.request.method == "POST"`
3. Pulsa "Start capturing".

**Con tcpdump (CLI):**
```bash
sudo tcpdump -i <interfaz> -A -s 0 'tcp port 80' -w captura_http.pcap
```

## 3. Iniciar sesión desde el cliente

En la máquina "Cliente", abre el navegador y ve a:

```
http://192.168.50.10/login.php
```

Inicia sesión con `user1` / `password1`.

## 4. Analizar la captura

En Wireshark, busca el paquete `POST /login.php`:
- Clic derecho → **Follow → HTTP Stream**.
- Verás el cuerpo del POST con algo como:
  `username=user1&password=password1`

Con tcpdump, revisa el .pcap:
```bash
tcpdump -A -r captura_http.pcap | grep -A 5 "POST /login.php"
```

## 5. Evidencia para el informe

Documenta, con capturas de pantalla:
- El filtro aplicado en Wireshark.
- El stream HTTP mostrando usuario y contraseña en texto plano.
- Marca de tiempo y IPs origen/destino (cliente → servidor).

Esto demuestra el problema que resuelve la Fase 2 (HTTPS): ver
[`https-phase.md`](https-phase.md).

## Consideraciones éticas y de alcance

- Este escenario debe ejecutarse **solo** en una red de laboratorio
  propia o explícitamente autorizada para pruebas.
- No reutilices las credenciales de prueba (`user1`/`user2`) ni la
  configuración HTTP-sin-cifrar fuera de este entorno aislado.
- La captura de tráfico de terceros sin autorización es ilegal en la
  mayoría de las jurisdicciones — este lab es para tu propio tráfico,
  en tu propia red controlada.

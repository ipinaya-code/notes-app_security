# App de Notas con Login (PHP) — Laboratorio HTTP vs HTTPS

Aplicación web mínima de notas con autenticación, pensada como laboratorio
didáctico de seguridad en redes: primero se expone por **HTTP sin cifrar**
para observar con Wireshark cómo viajan las credenciales en texto plano,
y después se migra a **HTTPS con certificado autofirmado** para mostrar
que ese mismo tráfico queda cifrado.

> ⚠️ **Uso exclusivo en laboratorio/red aislada.** Esta app usa HTTP sin
> cifrar a propósito en la Fase 1. No la expongas a redes públicas ni a
> Internet. Está pensada para un entorno controlado (ej. una VLAN de
> laboratorio con 3 VMs: servidor, cliente, atacante).

## Arquitectura

```
notes-app/
├── docker-compose.yml       # Orquesta nginx + php-fpm + mariadb
├── app/
│   ├── Dockerfile            # PHP 8.2-fpm + mysqli/pdo_mysql
│   ├── public/                # Document root servido por nginx
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   └── style.css  # Estilos propios (fuentes, cards, animaciones)
│   │   │   └── js/
│   │   │       └── app.js     # Mostrar/ocultar contraseña, confirmar borrado, etc.
│   │   ├── index.php
│   │   ├── login.php          # Formulario de login
│   │   ├── notes.php          # CRUD de notas (requiere sesión)
│   │   └── logout.php
│   └── src/
│       ├── db.php             # Conexión mysqli (prepared statements)
│       ├── auth.php           # Helpers de sesión
│       └── templates/
│           ├── header.php     # <head> + navbar compartidos (CDN Bootstrap/Tailwind)
│           └── footer.php     # Cierre de página + carga de app.js
├── db/
│   └── init.sql               # Esquema + usuarios de prueba
├── nginx/
│   ├── conf.d/
│   │   ├── default.conf               # Fase 1: HTTP, puerto 80
│   │   └── default-ssl.conf.disabled  # Fase 2: HTTPS, puerto 443
│   └── certs/
│       └── generate-cert.sh   # Genera certificado autofirmado
└── docs/
    ├── forensic-lab.md        # Guía del escenario de 3 máquinas
    └── https-phase.md         # Cómo activar la Fase 2 (HTTPS)
```

## Frontend

- **HTML**: generado por PHP a partir de plantillas compartidas en
  `app/src/templates/` (`header.php`/`footer.php`), reutilizadas por
  `login.php` y `notes.php` para no repetir el `<head>` ni la navbar.
- **CSS**: [Bootstrap 5](https://getbootstrap.com/) (componentes:
  botones, cards, alerts, inputs) + [Tailwind CSS](https://tailwindcss.com/)
  (utilidades de layout/espaciado) vía CDN, más estilos propios en
  `assets/css/style.css` (tipografía Poppins/Inter, degradados de
  marca, animaciones de entrada/salida).
- **JS**: `assets/js/app.js`, sin frameworks — mostrar/ocultar
  contraseña, confirmación animada antes de borrar una nota, y una
  pequeña animación de entrada para las tarjetas. Es solo mejora de
  UX; toda la validación real ocurre en el servidor (PHP).

## Usuarios de prueba

| Usuario | Contraseña  |
|---------|-------------|
| user1   | password1   |
| user2   | password2   |

Las contraseñas se almacenan en la base de datos con `password_hash()`
(bcrypt), como en cualquier app real — eso protege la base de datos si
se filtra, pero **no** protege el tráfico de red: en la Fase 1 (HTTP),
el formulario envía usuario y contraseña **en texto plano** dentro del
cuerpo del POST, que es exactamente lo que Wireshark captura.

## Levantar el entorno (Fase 1: HTTP)

Requiere Docker y Docker Compose.

```bash
cd notes-app
docker compose up -d --build
```

La app queda disponible en `http://<IP-del-servidor>/` (puerto 80).

Para detener y limpiar:

```bash
docker compose down -v
```

## Siguiente paso

- Para el escenario de captura con 3 máquinas, ver [`docs/forensic-lab.md`](docs/forensic-lab.md).
- Para activar HTTPS y comparar, ver [`docs/https-phase.md`](docs/https-phase.md).

## Notas de seguridad (para el informe del laboratorio)

- El login usa *prepared statements* (`mysqli`) para evitar inyección SQL.
- Las contraseñas se comparan con `password_verify()`, nunca en texto
  plano contra la BD.
- La sesión se regenera (`session_regenerate_id`) tras un login exitoso
  para mitigar *session fixation*.
- Lo que **no** se protege en la Fase 1 es el canal de transporte — por
  diseño, para fines del laboratorio — de ahí la Fase 2 con TLS.

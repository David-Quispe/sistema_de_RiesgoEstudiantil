# SMER — Sistema de Monitoreo y Evaluación de Riesgo Estudiantil

Sistema web de gestión académica orientado al seguimiento, detección y derivación de estudiantes en situación de riesgo. Desarrollado para uso institucional en TECSUP.

## ¿Qué hace?

- Registro y seguimiento de estudiantes en riesgo académico
- Gestión de entrevistas y derivaciones a soporte
- Generación de alertas automáticas según indicadores de riesgo
- Dashboard con estadísticas y tendencias por carrera
- Auditoría de acciones en el sistema
- Gestión de usuarios y roles

---

## Requisitos del sistema

Antes de instalar, asegurarse de tener lo siguiente:

| Requisito | Versión mínima | Notas |
|---|---|---|
| PHP | 8.2 | |
| Composer | 2.x | |
| Node.js | 18+ | |
| npm | 9+ | |
| Oracle XE | 21c | Base de datos principal |
| Oracle Instant Client | 21.x | Necesario para la extensión OCI8 |

### Extensiones PHP requeridas

```
oci8          ← crítica, para conectar con Oracle (requiere Oracle Instant Client)
pdo
pdo_oci       ← alternativa/complemento a oci8
mbstring
openssl
tokenizer
xml
ctype
json
bcmath
fileinfo
```

> En XAMPP para Windows: habilitar `extension=oci8_21` en `php.ini` y asegurarse de que Oracle Instant Client esté en el PATH del sistema.

---

## Stack tecnológico

### Backend
| Tecnología | Versión | Uso |
|---|---|---|
| PHP | ^8.2 | Lenguaje base |
| Laravel | ^12.0 | Framework principal |
| Filament | ^3.3 | Panel de administración |
| yajra/laravel-oci8 | ^12.11 | Conexión a Oracle DB |
| barryvdh/laravel-dompdf | ^3.1 | Generación de PDFs |

### Frontend
| Tecnología | Versión | Uso |
|---|---|---|
| Tailwind CSS | ^4.0 | Estilos |
| Vite | ^7.0 | Bundler |
| Axios | ^1.11 | Peticiones HTTP |

### Base de datos
| Tecnología | Uso |
|---|---|
| Oracle XE 21c | Base de datos principal |
| SQLite | Entorno de desarrollo local |

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd SMER

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate
```

Editar `.env` con las credenciales de Oracle:

```env
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=XE
DB_USERNAME="C##SMER"
DB_PASSWORD=tu_password
```

### Configurar la base de datos Oracle

Ejecutar los scripts en este orden desde SQL*Plus o SQL Developer:

```
1. database/oracle/00_crear_usuario_smer.sql   ← crea el usuario C##SMER
2. database/oracle/01_smer_schema.sql          ← crea todas las tablas
3. database/oracle/02_triggers_autoincremento.sql ← triggers de autoincremento
```

---

## Levantar en desarrollo

```bash
composer run dev
```

Levanta en paralelo: servidor Laravel, queue worker, log watcher y Vite.

Acceder en: `http://localhost:8000`

# WIDS - Web Intrusion Detection System

Este repositorio contiene el código fuente de un Sistema de Detección de Intrusos Web (WIDS) desarrollado como Trabajo de Fin de Grado. El sistema intercepta y analiza el tráfico HTTP en tiempo real para identificar firmas maliciosas y patrones anómalos, aislando el análisis de seguridad en procesos en segundo plano para no penalizar la latencia de la aplicación.

## Panel y Monitorización

## Arquitectura y Tecnologías

El proyecto está construido sobre Laravel 11 y PHP 8.2. Los componentes principales son:

* **Motor de detección asíncrono:** La evaluación de reglas y el registro de incidentes se derivan a colas de trabajo gestionadas por Redis y monitorizadas a través de Laravel Horizon.
* **Panel de control:** Desarrollado con Filament (basado en Livewire) para la gestión de incidentes, reglas activas y configuración de webhooks.
* **Alertas dinámicas:** Integración de envío de correos vía SMTP ante eventos críticos. Incluye un sistema de cooldown apoyado en caché para evitar la saturación por correos duplicados durante ataques masivos.
* **Soporte de Proxies:** El núcleo está configurado para extraer la IP de origen real de las cabeceras HTTP cuando se despliega detrás de túneles o balanceadores de carga (ej. Cloudflare).

## Despliegue local

Para levantar el entorno es necesario contar con PHP 8.2+, Composer, una base de datos (MySQL/SQLite) y un servidor Redis activo.

**1. Preparar el entorno**

```bash
git clone [https://github.com/tu-usuario/wids-tfg.git](https://github.com/tu-usuario/wids-tfg.git)
cd wids-tfg
composer install
cp .env.example .env
php artisan key:generate
```

**2. Configuración de servicios (.env)**

Es imprescindible configurar la conexión a Redis para el sistema de colas y el servidor SMTP para las alertas:

QUEUE_CONNECTION=redis
CACHE_STORE=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña


**3. Base de datos y ejecución**
```bash
php artisan migrate
php artisan db:seed --class=RuleSeeder
```

Para que el sistema funcione correctamente con su arquitectura asíncrona, se deben levantar tres procesos:

```bash
php artisan serve
```

```bash
brew services start redis
```

```bash
php artisan horizon
```



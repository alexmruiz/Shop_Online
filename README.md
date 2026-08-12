# Tienda AmR

**Prototipo de tienda online — Laravel + Livewire**

Tienda AmR es un prototipo de e-commerce desarrollado con Laravel 11 y Livewire 3, pensado como base para una tienda online con carrito, proceso de pago (integrado con Laravel Cashier / Stripe), generación de facturas en PDF y panel administrativo.

**Estado:** Proyecto en desarrollo / prototipo.

**Resumen rápido:**
- **Usuarios:** registro, autenticación y gestión de perfil.
- **Carrito & Checkout:** añadir/editar/eliminar ítems, cálculo de totales, checkout con Cashier.
- **Facturación:** generación de facturas en PDF (Dompdf) y envío por correo.
- **Administración:** CRUD de categorías y productos, gestión de usuarios y dashboard con métricas.

## Características (detallado)

### Para usuarios
- Registro e inicio de sesión (Breeze/laravel auth).
- Navegación por categorías y fichas de producto.
- Añadir, actualizar y eliminar productos del carrito.
- Proceso de checkout que crea pedidos y procesa cobros (integración con Laravel Cashier / Stripe configurado vía .env).
- Recepción de correo de confirmación de pedido (`OrderConfirmedMail`).
- Descarga de la factura en PDF una vez completada la compra.

### Carrito y pedidos
- Modelo y persistencia del carrito y líneas (`app/Models/Cart.php`, `CartItem.php`).
- Servicios de negocio: `CartService`, `CheckoutService`, `OrderService` (ubicados en `app/Services`).
- Repositorios para abstracción de acceso a datos (`app/Repositories`).

### Administración
- CRUD de categorías y productos (panel admin protegido por roles y policies).
- Asignación de roles `admin` / `user` y políticas de acceso (`app/Policies`).
- Dashboard con gráficas de productos más vendidos (Chart.js).

### Integraciones y utilidades
- Pagos: `laravel/cashier` (Stripe) — requiere configurar `STRIPE_KEY`, `STRIPE_SECRET`, etc. en `.env`.
- PDFs: `barryvdh/laravel-dompdf` para generar facturas limpias a partir de vistas Blade.
- Notificaciones por email y plantillas en `app/Mail`.

## Estructura del proyecto (resumen relevante)
- `app/Livewire/` — componentes Livewire para UI dinámica (carrito, producto, categorías, checkout).
- `app/Models/` — modelos Eloquent: `Product`, `Category`, `Cart`, `CartItem`, `Order`, `User`.
- `app/Services/` — lógica de negocio (servicios reutilizables como `CheckoutService`).
- `app/Repositories/` — abstracción de acceso a datos.
- `app/Providers/` — service providers registrados (p. ej. `CartServiceProvider`, `InvoiceServiceProvider`).
- `app/Facades/` — fachadas para acceso simplificado a servicios (p. ej. `Cart`, `InvoiceFacade`).
- `resources/views/` — vistas Blade y plantillas para PDF.
- `database/factories/`, `migrations/`, `seeders/` — helpers para pruebas y semillas.
- `tests/` — pruebas unitarias y funcionales.

## Tecnologías y dependencias clave
- PHP 8.2+
- Laravel 11.x
- Livewire 3.x + Livewire Volt
- Laravel Cashier (Stripe)
- barryvdh/laravel-dompdf
- MySQL
- Chart.js

Dependencias principales (ver `composer.json`): `laravel/framework`, `livewire/livewire`, `livewire/volt`, `laravel/cashier`, `barryvdh/laravel-dompdf`.

## Requisitos
- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (o equivalente) y credenciales en `.env`

## Instalación rápida (local)
1. Clona el repositorio y entra al proyecto.
2. Instala dependencias PHP:

```bash
composer install
```

3. Copia el `.env` y genera una key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configura la base de datos en `.env` y ejecuta migraciones y seeders:

```bash
php artisan migrate --seed
```

5. Instala dependencias Node y construye assets:

```bash
npm install
npm run dev
```

6. Ejecuta el servidor local:

```bash
php artisan serve
```

## Configuración de pagos (Cashier / Stripe)
- Añade tus credenciales en `.env`: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`.
- Revisa `config/cashier.php` y la documentación de Laravel Cashier para la integración completa.

## Generación de PDFs
- Las facturas se generan desde vistas Blade y se procesan con Dompdf. Revisa `app/Services/InvoiceService.php` y `app/Providers/InvoiceServiceProvider.php`.

## Pruebas
- Ejecutar pruebas:

```bash
php artisan test
```

## Contribuir
- Sigue el estándar PSR-12 y ejecuta `composer install` + `npm install` antes de contribuir.
- Usar `laravel/pint` para formateo: `./vendor/bin/pint`.

## Archivos y puntos de entrada útiles
- Rutas web: `routes/web.php` y `routes/auth.php`.
- Controladores: `app/Http/Controllers/`.
- Componentes Livewire: `app/Livewire/`.
- Servicio del carrito: `app/Services/CartService.php`.

## Licencia
Proyecto con licencia MIT (ver `composer.json`).

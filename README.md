### Documentación del Proyecto: Lemax Finanzas

# Descripción del Proyecto
- Nombre del Proyecto: Sistema de Administración de Créditos

- Descripción: Esta aplicación web permite la gestión completa de créditos, incluyendo clientes, vendedores de créditos, cobradores de pagos y los pagos mismos. Ofrece funcionalidades para el seguimiento de créditos, resúmenes mensuales, renovaciones y refinanciamientos. Los usuarios pueden tener roles diferentes, como administrador y asistente, con distintos niveles de acceso.

# Tecnologías Usadas:

Backend: Laravel (PHP).
Frontend: Vue.js, TypeScript.
Construcción y Gestión de Activos: Vite.
Gestión de Estado y Enrutamiento: Inertia.

Instalación
Requisitos
PHP: 8.2 o superior
Composer: 2.2 o superior
Node.js: 18.0 o superior
NPM/Yarn: Para manejar dependencias frontend

# Uso
 - Acceso
Administrador:

Usuario: admin@lemaxfinanzas
Contraseña: 123123123/
Asistente:

Usuario: asistente@lemaxfinanzas
Contraseña: 123123123/

Funcionalidades Principales

- Administración de Créditos

Crear, editar y eliminar créditos.
Visualizar resúmenes de créditos.
Renovaciones y Refinanciamientos.
Gestionar créditos renovados y refinanciados.

- Gestión de Clientes

Agregar y editar clientes.
Consultar información de clientes.

- Manejo de Vendedores y Cobradores:

Asignar clientes a vendedores.
Monitorear y gestionar cobradores.

- Pagos y Resúmenes

Registrar pagos y visualizar resúmenes.
Generar informes mensuales y por vendedor.


- Configuración de la Aplicación (Solo para Administradores)

Cambiar configuraciones globales de la aplicación.
Cambiar contraseñas.


# Estructura del Proyecto
Backend
app/Http/Controllers: Controladores para manejar la lógica de la aplicación.
app/Models: Modelos de Eloquent para interactuar con la base de datos.
database/migrations: Migraciones para definir la estructura de la base de datos.
routes/web.php: Definición de rutas para la aplicación.
Frontend
resources/js: Código fuente de Vue.js y TypeScript.

components: Componentes de Vue.js.
pages: Páginas de la aplicación gestionadas por Inertia.
app.js: Configuración de la aplicación Vue.js e Inertia.
vite.config.js: Configuración de Vite para el manejo de activos.

# Configuración
config: Archivos de configuración para Laravel.
.env: Variables de entorno.


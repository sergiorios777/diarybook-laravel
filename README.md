# 📘 DiaryBook - Sistema de Gestión Financiera

**DiaryBook** es una aplicación web moderna diseñada para el control exhaustivo del flujo de caja, registro de gastos e ingresos, y arqueo de valores. Construido sobre la robustez de **Laravel 12** y la agilidad visual de **Tailwind CSS**.

![Estado](https://img.shields.io/badge/Estado-En_Desarrollo-blue)
![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)

## 🚀 Características Principales

### 📊 Dashboard Interactivo
- Visualización de saldo total en tiempo real.
- Gráfico de **evolución diaria** (últimos 12 días) para micro-gestión.
- Gráfico de **evolución mensual** para tendencias a largo plazo.
- Resumen de ingresos y gastos del mes actual.

### 💰 Gestión Inteligente de Transacciones
- **Motor de Asignación Automática:** El sistema sugiere automáticamente la categoría basándose en la descripción ingresada (usando palabras clave, expresiones regulares y lógica de montos positivos/negativos).
- Registro rápido con atajos de teclado y enfoque automático.
- Historial completo con filtros por fecha, cuenta y categoría.

### 🧮 Arqueo de Caja (Cash Count)
- Herramienta integrada para el conteo físico de dinero.
- Calculadora de billetes y monedas en tiempo real.
- **Comparación en vivo:** Conecta con el saldo del sistema mediante AJAX para mostrar diferencias (sobrantes/faltantes) sin recargar la página.
- Impresión de tickets de arqueo.

### 📈 Reportes Profesionales
- **Reporte Semanal:** Vista matricial de ingresos y gastos por día.
- **Estilos de Impresión:** CSS optimizado (`@media print`) para generar documentos limpios, en blanco y negro, listos para firmar, ocultando la interfaz web.
- Exportación a PDF nativa (DomPDF).

### 🎨 Experiencia de Usuario (UX)
- **Modo Oscuro/Claro:** Persistente y sin parpadeos (Anti-flicker script).
- Diseño totalmente **Responsive** (Móvil y Escritorio).
- Sidebar con scroll independiente.

---

## 🛠️ Tecnologías Utilizadas

- **Backend:** Laravel 12
- **Frontend:** Blade, Tailwind CSS v4
- **Scripting:** Alpine.js (para interactividad ligera y modales)
- **Gráficos:** Chart.js
- **Base de Datos:** MySQL / SQLite
- **PDF:** Laravel DomPDF

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para desplegar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/TuUsuario/diarybook-laravel.git](https://github.com/TuUsuario/diarybook-laravel.git)
    cd diarybook-laravel
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de Frontend:**
    ```bash
    npm install
    ```

4.  **Configurar el entorno:**
    - Duplica el archivo de ejemplo:
      ```bash
      cp .env.example .env
      ```
    - Abre el archivo `.env` y configura tus credenciales de base de datos (`DB_DATABASE`, etc.).

5.  **Generar clave de aplicación:**
    ```bash
    php artisan key:generate
    ```

6.  **Ejecutar migraciones (Base de Datos):**
    Esto creará las tablas necesarias (Accounts, Categories, Transactions, CategoryMatchers, etc.).
    ```bash
    php artisan migrate
    ```
    *(Opcional: Si tienes seeders creados)*
    ```bash
    php artisan db:seed
    ```

7.  **Compilar activos y ejecutar:**
    Necesitas dos terminales:
    
    *Terminal 1 (Vite - Estilos):*
    ```bash
    npm run dev
    ```
    
    *Terminal 2 (Servidor Laravel):*
    ```bash
    php artisan serve
    ```

8.  **¡Listo!**
    Accede a `http://localhost:8000` en tu navegador.

---

## 📖 Uso del Motor de Reglas (Category Matchers)

Para que la "magia" de la asignación automática funcione en el formulario de transacciones, debes poblar la tabla `category_matchers`.

Ejemplo de lógica:
- Si la descripción contiene "Venta", asignar a categoría "Ingresos por Ventas".
- Si la descripción contiene "Uber" Y el monto es negativo, asignar a "Transporte".

*(Puedes gestionar esto desde la base de datos o crear un Seeder específico).*

---

## 📸 Capturas de Pantalla

*(Espacio reservado para agregar imágenes de tu Dashboard, Formulario y Reporte)*

---

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [MIT license](https://opensource.org/licenses/MIT).
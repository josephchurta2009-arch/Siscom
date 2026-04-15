# 🧑‍💻 Manual del Programador – Sistema CISCOM

---

## 📌 1. Introducción

El sistema **CISCOM** es una aplicación backend desarrollada para gestionar información comunitaria, incluyendo personas, ayudas y entregas.

Este manual está dirigido a desarrolladores que deseen instalar, entender, modificar o escalar el sistema.

---

## 🏗️ 2. Arquitectura del Sistema

El proyecto sigue una arquitectura modular basada en Node.js:


backend/
└── src/
├── index.ts
├── db.ts
└── routes/


### 🔧 Tecnologías utilizadas

- Node.js
- Express
- MySQL
- TypeScript
- dotenv
- cors

---

## ⚙️ 3. Requisitos del Sistema

Antes de ejecutar el proyecto, asegúrate de tener instalado:

- Node.js (v16 o superior)
- MySQL Server
- Git

---

## 📥 4. Instalación del Proyecto

### 4.1 Clonar repositorio

```bash
git clone https://github.com/josephchurta2009-arch/Siscom.git
cd siscom-admin
4.2 Instalar dependencias
npm install
🔐 5. Configuración de Variables de Entorno

Crear un archivo .env en la raíz del backend:

PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=siscom
▶️ 6. Ejecución del Proyecto
Modo desarrollo
npm run dev
Modo producción
npm start
📁 7. Estructura del Proyecto
src/
│
├── index.ts              # Punto de entrada del servidor
├── db.ts                 # Configuración de conexión a MySQL
│
└── routes/
    ├── personas.routes.ts
    ├── ayudas.routes.ts
    └── entregas.routes.ts
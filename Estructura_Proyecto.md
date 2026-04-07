# Estructura del Proyecto - PHP CRUD paises_del_mundo

## Fase 1: Instalación y Configuración de Servidores

### 1.1 Instalar XAMPP
- Descargar desde https://www.apachefriends.org/
- Componentes: Apache, MySQL, PHP, phpMyAdmin

### 1.2 Iniciar Servicios
- Apache (puerto 80)
- MySQL (puerto 3306)

---

## Fase 2: Base de Datos

### 2.1 phpMyAdmin
- URL: http://localhost/phpmyadmin
- Usuario: root
- Contraseña: (vacía)

### 2.2 Crear Base de Datos
```sql
CREATE DATABASE paises;
USE paises;
```

### 2.3 Crear Tabla
```sql
CREATE TABLE paises_del_mundo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_del_pais VARCHAR(100) NOT NULL,
    continente VARCHAR(50) NOT NULL,
    capital VARCHAR(100) NOT NULL,
    habitantes BIGINT NOT NULL,
    superficie BIGINT NOT NULL,
    pib_2024_usd DECIMAL(15,2) NOT NULL,
    emisiones_co2_2024_toneladas BIGINT NOT NULL,
    bandera VARCHAR(255),
    fecha_admision_onu DATE
);
```

---

## Fase 3: Proyecto

### 3.1 Ubicación
- Carpeta: C:\xampp\htdocs\Paises\
- Archivos: index.php, php/funciones.php, css/estilos.css

### 3.2 Acceso
- URL: http://localhost/Paises/index.php

---

## Fase 4: Estructura de Archivos

### Archivos del Proyecto
| Archivo | Descripción |
|---------|-------------|
| index.php | Archivo principal (antes datos.php) - Tabla de países |
| juego.php | Juego general con preguntas de capital, continente y bandera |
| juegoContinente.php | Juego por continente seleccionado |
| juegoBandera.php | Juego solo de banderas (todas o por continente) |
| php/funciones.php | Funciones PHP (conexión, validación, formateo) |
| php/operaciones.php | Operaciones CRUD |
| css/estilos.css | Estilos CSS (tabla, badges, contenedores) |
| css/themes/ | Temas Bootstrap (quartz, solar, slate, sketchy) |
| icons/ | Iconos (globo-terraqueo.png, bola.png)

### Conexión MySQL
- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: paises

### Columnas de la Tabla
| Campo | Tipo |
|-------|------|
| id | INT AUTO_INCREMENT |
| nombre_del_pais | VARCHAR(100) |
| continente | VARCHAR(50) |
| capital | VARCHAR(100) |
| habitantes | BIGINT |
| superficie | BIGINT |
| pib_2024_usd | DECIMAL(15,2) |
| emisiones_co2_2024_toneladas | BIGINT |
| bandera | VARCHAR(255) |
| fecha_admision_onu | DATE |
| Wikipedia | VARCHAR(500) |

### Funcionalidades
1. Listar países en tabla con PHP (renderizado del lado del servidor)
2. Buscar por país, capital o continente (filtro con formulario GET)
3. Ordenar por cualquier columna (enlaces GET)
4. Agregar nuevo país (modal)
5. Editar país existente (modal)
6. Eliminar país (confirmación)
7. Cambio de tema (Quartz, Solar, Slate, Sketchy)
8. Validación de URLs de banderas con try-catch
9. Redirección automática tras operaciones exitosas
10. Contador total de países en el título
11. Hipervínculo a Wikipedia desde el nombre del país

### Juegos de Preguntas
1. **juego.php** - Juego general con 3 tipos de preguntas:
   - ¿Cuál es la capital de [país]?
   - ¿De qué país es capital [ciudad]?
   - ¿A qué país pertenece esta bandera?

2. **juegoContinente.php** - Juego por continente específico:
   - El usuario selecciona un continente
   - Preguntas sobre países de ese continente
   - Mismos 3 tipos de preguntas

3. **juegoBandera.php** - Juego de solo banderas:
   - El usuario puede seleccionar un continente o "todos"
   - Un solo tipo de pregunta: ¿A qué país pertenece esta bandera?

### Sistema de Dificultad
| Nivel | Errores permitidos |
|-------|-------------------|
| Fácil | 7 errores |
| Normal | 5 errores |
| Difícil | 3 errores |

### Opciones de Número de Preguntas
- 10 preguntas
- 20 preguntas (predeterminado)

### Función verificarPartida()
- Se ejecuta al pulsar "Siguiente Pregunta"
- Verifica si errores >= dificultad → partida perdida
- Verifica si aciertos >= numPreguntas → partida ganada
- Al finalizar muestra resultados con opciones para jugar de nuevo o cambiar configuración

### Temas Bootstrap
- Tema predeterminado: Quartz
- Temas disponibles: Solar, Slate, Sketchy, Quartz
- Archivos locales en: css/themes/
- Selección guardada en localStorage

### Estilos de Continentes
| Continente | Color |
|------------|-------|
| Europa | Azul (#3498db) |
| Asia | Rojo (#e74c3c) |
| América del Norte | Verde (#2ecc71) |
| América del Sur | Naranja (#f39c12) |
| África | Morado (#9b59b6) |
| Océanía | Turquesa (#1abc9c) |

### Formato de Datos
- Separador de miles: punto (formato español es-ES)
- Fecha ONU: dd/mm/yyyy
- Tamaño de fuente tabla: 1.10em (10% mayor)
- Ancho mínimo tabla: 1620px
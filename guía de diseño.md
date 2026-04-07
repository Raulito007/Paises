# Guía de Diseño - Países del Mundo

## Temas Bootstrap Disponibles

| Tema | Descripción | Color iconos |
|------|-------------|--------------|
| Quartz | Predeterminado - Efecto cristalino | Blanco |
| Solar | Colores cálidos basados en Solarized | Blanco |
| Slate | Tonos grises oscuros | Blanco |
| Sketchy | Estilo dibujo a mano | Negro |

## Estilos Personalizados

### Contenedor de Banderas
```css
#contenedor-bandera {
    background-color: #ffffff !important;
    padding: 5px !important;
    display: inline-block !important;
    border-radius: 4px !important;
    border: 2px solid black;
}
```
- Fondo blanco para todos los temas
- Padding de 5px para crear separación
- Borde negro de 2px
- Bordes redondeados para mejor apariencia

### Iconos de Tema Claro
```css
.theme-light-icon img { filter: invert(1); }
```
- Aplica a los temas: Solar, Slate, Quartz
- Invierte el color de los iconos a blanco

### Badges de Continentes
```css
.badge-europa { background-color: #3498db; color: white; }
.badge-asia { background-color: #e74c3c; color: white; }
.badge-america-norte { background-color: #2ecc71; color: white; }
.badge-america-sur { background-color: #f39c12; color: white; }
.badge-africa { background-color: #9b59b6; color: white; }
.badge-oceania { background-color: #1abc9c; color: white; }
```

### Tabla
```css
#tablaPaises, #tablaPaises td, #tablaPaises th { font-size: 1.10em; }
#tablaPaises { min-width: 1620px; }
th a { color: white; text-decoration: none; }
th a:hover { text-decoration: underline; }
```
- Tamaño de fuente aumentado un 10%
- Ancho mínimo de 1620px
- Encabezados con enlaces para ordenar

## Paleta de Colores por Tema

### Quartz (Predeterminado)
- Fondo principal: #6c757d (gris oscuro)
- Fondo tablas: Según tema

### Solar
- Fondo principal: Según tema

### Slate
- Fondo principal: Según tema
- Tonos oscuros

### Sketchy
- Fondo principal: Según tema
- Estilo hand-drawn

## Estructura Visual

### Tabla
- Bordes visibles en todas las celdas
- Fondo oscuro en cabecera (table-dark)
- 11 columnas: País (con enlace Wikipedia), Capital, Continente, Habitantes, Superficie, PIB, CO₂, Bandera, ONU, Opciones

### Botones de Opciones
- Editar: ✏️ (btn-primary)
- Eliminar: 🗑️ (btn-danger)

### Modal de Edición
- Fondo semitransparente: rgba(0,0,0,0.5)
- Diseño responsivo con Bootstrap

## Notas
- Los temas se cargan desde archivos locales en `css/themes/`
- La selección de tema se guarda en localStorage
- El tema predeterminado es Quartz

## Estructura de Archivos

### PHP
- Archivo: `php/funciones.php`
- Funciones: conectarBD(), validarUrl(), validarEntero(), obtenerBadgeClass(), formatearFecha(), formatearNumero()

### CSS
- Archivo: `css/estilos.css`
- Contiene: estilos de tabla, badges de continentes, contenedor de banderas, tema claro

### Tema Principal
- CSS externo: `css/estilos.css` + `css/themes/{quartz,solar,slate,sketchy}.min.css`

## Validación de Banderas

### Lógica de Validación
- Al guardar un país (crear/actualizar), se valida la URL de la bandera
- Se usa try-catch con `file_get_contents` y timeout de 5 segundos
- Si la URL devuelve contenido con `<` (error/warning de PHP), se descarta
- Si la URL es inválida o genera excepción, se guarda vacía

### Visualización en Tabla
- Si la bandera es válida: muestra la imagen (ancho 50px)
- Si la bandera es inválida o vacía: muestra "NO DISPONIBLE" en gris

## Funcionalidades PHP

### Búsqueda
- Formulario GET con input de texto
- Busca por: país, capital, continente
- Envía parámetros: ?search=texto

### Ordenación
- Enlaces en encabezados de columna
- Parámetros: ?sort=campo&dir=asc|desc
- Indicador visual ▲/▼

### Formato de Datos
- Separador de miles: punto (formato PHP number_format)
- Fecha ONU: conversión de yyyy-mm-dd a dd/mm/yyyy con date()

## Contador de Países
- Mostrado en el título: "Países del Mundo (X países)"
- Se obtiene de $result->num_rows

## Hipervínculos Wikipedia
- Campo de la base de datos: Wikipedia
- Se valida que no contenga `<` (errores)
- Se muestra enlace en el nombre del país
- Se abre en nueva pestaña (target="_blank")

---

# Juegos de Preguntas

## Navegación entre Juegos
Todos los juegos incluyen botones de navegación:
- **Ver Tabla** → index.php
- **🎮 Juego General** → juego.php
- **🌍 Continentes** → juegoContinente.php
- **🏳️ Banderas** → juegoBandera.php

## común de los Juegos
- Todos usan sesiones PHP para mantener el estado
- Parámetro `?nuevo=1` para reiniciar sesiones
- Footer con "Proyecto realizado por Raulito" y datos de la ONU
- Selector de tema en todos los juegos

## Estilos CSS de Juegos
```css
.bola-titulo { width: 75px; height: 75px; vertical-align: middle; margin-right: 10px; opacity: 0.70; mix-blend-mode: multiply; }
.pregunta-box { background: white; padding: 30px; border-radius: 10px; max-width: 700px; margin: 0 auto; color: #333; }
.pregunta-box h2 { margin-bottom: 25px; color: #333; }
.barra-progreso { height: 30px; background: #e9ecef; border-radius: 5px; margin-bottom: 20px; overflow: hidden; }
.barra-progreso .progreso { height: 100%; background: #28a745; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
.resultado-box { text-align: center; padding: 30px; }
.resultado-box .correcto { color: #28a745; font-size: 1.5em; font-weight: bold; }
.resultado-box .incorrecto { color: #dc3545; font-size: 1.5em; font-weight: bold; }
```

## juego.php - Juego General
- Preguntas aleatorias de capital, continente o bandera
- No hay filtro por continente

## juegoContinente.php - Juego por Continentes
- Selector de continente obligatorio
- 3 tipos de preguntas (capital, país-capital, bandera)
- Excluye tipo de pregunta "continente" (no tiene sentido)

## juegoBandera.php - Juego de Banderas
- Selector de continente o "todos los continentes"
- Un solo tipo de pregunta: identificar país por bandera
- Opción "todos" para jugar con todos los continentes

## Exclusión de Encabezados en SQL
Los juegos excluyen los nombres de columnas de la tabla del algoritmo de preguntas:
```sql
WHERE nombre_del_pais NOT IN ('nombre_del_pais', 'capital', 'continente', ...)
```
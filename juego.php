<?php
session_start();

if (isset($_GET['nuevo']) && $_GET['nuevo'] === '1') {
    // Limpiar todas las variables del juego
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['nivel'] = '';
    $_SESSION['dificultad'] = 5;
    $_SESSION['num_preguntas'] = 20;
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['juego_terminado'] = false;
    $_SESSION['resultado'] = null;
}

require_once 'php/funciones.php';
require_once 'php/operaciones.php';

$conn = conectarBD();
$message = '';
$mostrarResultado = false;

// Función para verificar el estado de la partida
function verificarPartida($aciertos, $errores, $dificultad, $numPreguntas) {
    $resultado = [
        'terminada' => false,
        'ganada' => false,
        'mensaje' => ''
    ];
    
    if ($errores >= $dificultad) {
        $resultado['terminada'] = true;
        $resultado['ganada'] = false;
        $resultado['mensaje'] = "¡Has perdido! Has cometido {$errores} errores (límite: {$dificultad}).";
    }
    elseif ($aciertos >= $numPreguntas) {
        $resultado['terminada'] = true;
        $resultado['ganada'] = true;
        $resultado['mensaje'] = "¡Felicidades! Has completado el juego con {$aciertos} aciertos y {$errores} errores.";
    }
    elseif ($aciertos + $errores >= $numPreguntas) {
        $resultado['terminada'] = true;
        $resultado['ganada'] = true;
        $resultado['mensaje'] = "¡Felicidades! Has completado el juego con {$aciertos} aciertos y {$errores} errores.";
    }
    
    return $resultado;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar'])) {
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['nivel'] = $_POST['nivel'];
    $_SESSION['dificultad'] = $_POST['nivel'] === 'facil' ? 7 : ($_POST['nivel'] === 'normal' ? 5 : 3);
    $_SESSION['num_preguntas'] = intval($_POST['num_preguntas']);
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['resultado'] = null;
    $_SESSION['juego_terminado'] = false;
    
    header('Location: juego.php');
    exit;
}

if (isset($_POST['contestar'])) {
    $respuestaCorrectaSession = $_SESSION['pregunta_actual']['correcta'] ?? '';
    $respuestaUsuario = $_POST['respuesta'] ?? '';
    
    if ($respuestaUsuario === $respuestaCorrectaSession) {
        $_SESSION['aciertos']++;
        $resultado = 'correcto';
    } else {
        $_SESSION['errores']++;
        $resultado = 'incorrecto';
    }
    
    $_SESSION['resultado'] = $resultado;
    $_SESSION['resultado_correcta'] = $respuestaCorrectaSession;
    $_SESSION['pregunta_actual'] = null;
    
    // Redirigir para que revise el estado del juego
    header('Location: juego.php');
    exit;
}

if (isset($_POST['siguiente'])) {
    $_SESSION['resultado'] = null;
    $_SESSION['resultado_correcta'] = null;
    $_SESSION['pregunta_actual'] = null;
    
    // Verificar si es la última pregunta antes de redirigir
    $aciertosCheck = $_SESSION['aciertos'];
    $erroresCheck = $_SESSION['errores'];
    $numPreguntasCheck = $_SESSION['num_preguntas'] ?? 20;
    $dificultadCheck = $_SESSION['dificultad'];
    
    // Si el juego ya terminó, marcar como terminado
    if ($aciertosCheck >= $numPreguntasCheck || $erroresCheck >= $dificultadCheck) {
        $_SESSION['juego_terminado'] = true;
    }
    // Si no ha terminado, generar siguiente pregunta
    else {
        header('Location: juego.php');
        exit;
    }
}

if (isset($_POST['terminar'])) {
    $message = "Has terminado con " . $_SESSION['aciertos'] . " aciertos y " . $_SESSION['errores'] . " errores.";
    $_SESSION['juego_terminado'] = true;
}

if (isset($_POST['repetir'])) {
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['resultado'] = null;
    $_SESSION['juego_terminado'] = false;
    
    header('Location: juego.php');
    exit;
}

if (isset($_POST['cambiar_dificultad'])) {
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['nivel'] = '';
    $_SESSION['dificultad'] = 5;
    $_SESSION['num_preguntas'] = 20;
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['resultado'] = null;
    $_SESSION['juego_terminado'] = false;
    
    header('Location: juego.php');
    exit;
}

if (isset($_POST['iniciar'])) {
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['nivel'] = $_POST['nivel'];
    $_SESSION['dificultad'] = $_POST['nivel'] === 'facil' ? 7 : ($_POST['nivel'] === 'normal' ? 5 : 3);
    $_SESSION['num_preguntas'] = intval($_POST['num_preguntas']);
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['resultado'] = null;
    $_SESSION['juego_terminado'] = false;
    
    header('Location: juego.php');
    exit;
}

if (isset($_POST['cambiar_dificultad'])) {
    $_SESSION['aciertos'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['nivel'] = '';  // Resetear nivel para mostrar formulario de inicio
    $_SESSION['dificultad'] = 5;
    $_SESSION['num_preguntas'] = 20;
    $_SESSION['preguntas_usadas'] = [];
    $_SESSION['pregunta_actual'] = null;
    $_SESSION['resultado'] = null;
    $_SESSION['juego_terminado'] = false;
    
    header('Location: juego.php?nuevo=1');
    exit;
}

if (isset($_SESSION['resultado']) && $_SESSION['resultado'] !== null && empty($_SESSION['juego_terminado'])) {
    $mostrarResultado = true;
    $resultado = $_SESSION['resultado'];
    $respuestaCorrecta = $_SESSION['resultado_correcta'] ?? '';
}

$errores = $_SESSION['errores'];
$aciertos = $_SESSION['aciertos'];
$dificultad = $_SESSION['dificultad'];
$numPreguntas = $_SESSION['num_preguntas'] ?? 20;
$preguntasRestantes = $numPreguntas - ($errores + $aciertos);

// Usar la función verificarPartida
$estadoPartida = verificarPartida($aciertos, $errores, $dificultad, $numPreguntas);

// Determinar qué mostrar
$mostrarFormulario = false;
$mostrarResultado = false;
$resultado = '';

if ($estadoPartida['terminada']) {
    $mostrarResultado = true;
    $resultado = $estadoPartida['ganada'] ? 'ganado' : 'perdido';
    $message = $estadoPartida['mensaje'];
    $_SESSION['juego_terminado'] = true;
    $_SESSION['resultado'] = null;
    $_SESSION['resultado_correcta'] = null;
} elseif (empty($_SESSION['nivel'])) {
    $mostrarFormulario = true;
} elseif (!empty($_SESSION['resultado'])) {
    $mostrarResultado = true;
    $resultado = $_SESSION['resultado'];
    $respuestaCorrecta = $_SESSION['resultado_correcta'] ?? '';
} elseif (empty($_SESSION['pregunta_actual']) && $preguntasRestantes > 0) {
    $result = $conn->query("SELECT nombre_del_pais, capital, continente, bandera FROM paises_del_mundo WHERE nombre_del_pais NOT IN ('nombre_del_pais', 'capital', 'continente', 'habitantes', 'superficie', 'pib_2024_usd', 'emisiones_co2_2024_toneladas', 'fecha_admision_onu')");
    $paises = $result->fetch_all(MYSQLI_ASSOC);
    
    if (count($paises) >= 4 && count($_SESSION['preguntas_usadas'] ?? []) < count($paises)) {
        do {
            $indice = array_rand($paises);
            $paisPrincipal = $paises[$indice];
        } while (in_array($paisPrincipal['nombre_del_pais'], $_SESSION['preguntas_usadas'] ?? []));
        
        $_SESSION['preguntas_usadas'][] = $paisPrincipal['nombre_del_pais'];
        
        $tipoPregunta = rand(1, 3);
        $opciones = [];
        $respuestaCorrecta = '';
        
        if ($tipoPregunta === 1) {
            $respuestaCorrecta = $paisPrincipal['capital'];
            $opciones[] = $respuestaCorrecta;
            $demas = array_filter($paises, fn($p) => $p['capital'] !== $respuestaCorrecta && !empty($p['capital']));
            shuffle($demas);
            foreach (array_slice($demas, 0, 3) as $p) {
                $opciones[] = $p['capital'];
            }
            $pregunta = "¿Cuál es la capital de {$paisPrincipal['nombre_del_pais']}?";
            shuffle($opciones);
        } elseif ($tipoPregunta === 2) {
            $respuestaCorrecta = $paisPrincipal['continente'];
            $opciones[] = $respuestaCorrecta;
            $continentes = ['Europa', 'Asia', 'América del Norte', 'América del Sur', 'África', 'Oceanía'];
            $continentes = array_filter($continentes, fn($c) => $c !== $respuestaCorrecta);
            shuffle($continentes);
            foreach (array_slice($continentes, 0, 3) as $c) {
                $opciones[] = $c;
            }
            $pregunta = "¿En qué continente se encuentra {$paisPrincipal['nombre_del_pais']}?";
            shuffle($opciones);
        } else {
            $respuestaCorrecta = $paisPrincipal['nombre_del_pais'];
            $opciones[] = $respuestaCorrecta;
            $demas = array_filter($paises, fn($p) => $p['nombre_del_pais'] !== $respuestaCorrecta);
            shuffle($demas);
            foreach (array_slice($demas, 0, 3) as $p) {
                $opciones[] = $p['nombre_del_pais'];
            }
            $pregunta = "¿Qué país tiene esta bandera?";
            shuffle($opciones);
        }
        
        $_SESSION['pregunta_actual'] = [
            'correcta' => $respuestaCorrecta,
            'pregunta' => $pregunta,
            'opciones' => $opciones,
            'tipo' => $tipoPregunta,
            'pais' => $paisPrincipal
        ];
        
        $pregunta = $pregunta;
        $opciones = $opciones;
        $tipoPregunta = $tipoPregunta;
        $paisPrincipal = $paisPrincipal;
    } elseif ($preguntasRestantes > 0) {
        // No hay más preguntas disponibles
        $mostrarResultado = true;
        $resultado = 'ganado';
        $message = "No hay más preguntas disponibles.";
        $_SESSION['juego_terminado'] = true;
    }
}

if (!empty($_SESSION['pregunta_actual'])) {
    $pregunta = $_SESSION['pregunta_actual']['pregunta'];
    $opciones = $_SESSION['pregunta_actual']['opciones'];
    $respuestaCorrecta = $_SESSION['pregunta_actual']['correcta'];
    $tipoPregunta = $_SESSION['pregunta_actual']['tipo'];
    $paisPrincipal = $_SESSION['pregunta_actual']['pais'];
    $mostrarFormulario = true;
}

$errores = $_SESSION['errores'];
$aciertos = $_SESSION['aciertos'];
$dificultad = $_SESSION['dificultad'];
$numPreguntas = $_SESSION['num_preguntas'] ?? 20;
$preguntasRestantes = $numPreguntas - ($errores + $aciertos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Países</title>
    <link rel="icon" href="icons/globo-terraqueo.png" type="image/png">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/themes/quartz.min.css" id="theme-link">
    <style>
        .bola-titulo { width: 75px; height: 75px; vertical-align: middle; margin-right: 10px; opacity: 0.70; mix-blend-mode: multiply; }
        .opcion-btn { display: block; width: 100%; padding: 15px; margin: 10px 0; font-size: 1.1em; text-align: left; }
        .pregunta-box { background: white; padding: 30px; border-radius: 10px; max-width: 700px; margin: 0 auto; color: #333; }
        .pregunta-box h2 { margin-bottom: 25px; color: #333; }
        .barra-progreso { height: 30px; background: #e9ecef; border-radius: 5px; margin-bottom: 20px; overflow: hidden; }
        .barra-progreso .progreso { height: 100%; background: #28a745; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .resultado-box { text-align: center; padding: 30px; }
        .resultado-box .correcto { color: #28a745; font-size: 1.5em; font-weight: bold; }
        .resultado-box .incorrecto { color: #dc3545; font-size: 1.5em; font-weight: bold; }
    </style>
</head>
<body class="bg-light" style="background-color: #6c757d;">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0"><img src="icons/bola.png" alt="" class="bola-titulo">Juego de Países</h1>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-info me-3">Ver Tabla</a>
                <a href="juegoContinente.php?nuevo=1" class="btn btn-warning me-3">🌍 Continentes</a>
                <a href="juegoBandera.php?nuevo=1" class="btn btn-warning me-3">🏳️ Banderas</a>
                <label for="tema" class="me-2 fw-bold">TEMA:</label>
                <select id="tema" class="form-select" style="width: auto;">
                    <option value="quartz" selected>Quartz</option>
                    <option value="solar">Solar</option>
                    <option value="slate">Slate</option>
                    <option value="sketchy">Sketchy</option>
                </select>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= strpos($message, 'perdido') !== false ? 'danger' : 'success' ?>"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($mostrarFormulario && empty($_SESSION['nivel'])): ?>
        <div class="pregunta-box">
            <h2 class="text-center">🎮 Bienvenido al Juego</h2>
            <p class="text-center">Responde 20 preguntas sobre países del mundo.</p>
            <p class="text-center mb-4"><strong>Tipos de preguntas:</strong></p>
            <ul>
                <li>¿Cuál es la capital de un país?</li>
                <li>¿En qué continente se encuentra un país?</li>
                <li>¿Qué país tiene esta bandera?</li>
            </ul>
            <form method="POST" class="text-center">
                <div class="mb-3">
                    <label class="form-label fw-bold d-block" style="color: #333;">Selecciona dificultad:</label>
                    <select name="nivel" class="form-select form-select-lg" style="max-width: 300px; margin: 0 auto; font-size: 1.1em; color: #333; background-color: white;">
                        <option value="facil">Fácil (7 errores)</option>
                        <option value="normal" selected>Normal (5 errores)</option>
                        <option value="dificil">Difícil (3 errores)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold d-block" style="color: #333;">Número de preguntas:</label>
                    <select name="num_preguntas" class="form-select form-select-lg" style="max-width: 300px; margin: 0 auto; font-size: 1.1em; color: #333; background-color: white;">
                        <option value="10">10 preguntas</option>
                        <option value="20" selected>20 preguntas</option>
                    </select>
                </div>
                <button type="submit" name="iniciar" class="btn btn-success btn-lg">🎮 Iniciar Juego</button>
            </form>
        </div>
        <?php elseif ($mostrarResultado && !empty($_SESSION['juego_terminado'])): ?>
        <div class="pregunta-box">
            <div class="resultado-box">
                <?php if ($resultado === 'ganado'): ?>
                    <div class="correcto">🎉 ¡Felicidades!</div>
                    <p>Has completado el juego con <strong><?= $aciertos ?></strong> aciertos y <strong><?= $errores ?></strong> errores.</p>
                <?php else: ?>
                    <div class="incorrecto">💔 ¡Has perdido!</div>
                    <p>Has cometido <strong><?= $errores ?></strong> errores (límite: <?= $dificultad ?>).</p>
                    <p>Aciertos: <strong><?= $aciertos ?></strong></p>
                <?php endif; ?>
            </div>
            <form method="POST" class="text-center mt-4">
                <input type="hidden" name="nivel" value="<?= $_SESSION['nivel'] ?>">
                <button type="submit" name="repetir" class="btn btn-success btn-lg">🔄 Jugar de Nuevo</button>
            </form>
            <form method="POST" class="text-center mt-3">
                <button type="submit" name="cambiar_dificultad" class="btn btn-outline-primary">Cambiar dificultad</button>
            </form>
        </div>
        <?php elseif ($mostrarResultado): ?>
        <div class="pregunta-box">
            <div class="resultado-box">
                <?php if ($resultado === 'correcto'): ?>
                    <div class="correcto">✅ ¡Correcto!</div>
                <?php else: ?>
                    <div class="incorrecto">❌ Incorrecto</div>
                    <p>La respuesta correcta era: <strong><?= htmlspecialchars($respuestaCorrecta) ?></strong></p>
                <?php endif; ?>
            </div>
            <form method="POST" class="text-center mt-4">
                <button type="submit" name="siguiente" class="btn btn-primary btn-lg">Siguiente Pregunta</button>
            </form>
        </div>
        <?php elseif ($mostrarFormulario && $preguntasRestantes > 0): ?>
        <div class="pregunta-box">
            <div class="d-flex justify-content-between mb-3">
                <span><strong>Aciertos:</strong> <span class="text-success"><?= $aciertos ?></span></span>
                <span><strong>Errores:</strong> <span class="text-danger"><?= $errores ?>/<?= $dificultad ?></span></span>
                <span><strong>Restantes:</strong> <?= $preguntasRestantes ?></span>
            </div>
            <div class="barra-progreso">
                <div class="progreso" style="width: <?= ($aciertos / $numPreguntas) * 100 ?>%"><?= $aciertos ?>/<?= $numPreguntas ?></div>
            </div>
            
            <h2 class="text-center"><?= $pregunta ?></h2>
            
            <?php if ($tipoPregunta === 3 && !empty($paisPrincipal['bandera'])): ?>
            <div class="text-center mb-4">
                <div id="contenedor-bandera">
                    <img src="<?= htmlspecialchars($paisPrincipal['bandera']) ?>" alt="Bandera" style="width: 150px;">
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <?php foreach ($opciones as $opcion): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="respuesta" id="respuesta_<?= htmlspecialchars($opcion) ?>" value="<?= htmlspecialchars($opcion) ?>">
                        <label class="form-check-label" for="respuesta_<?= htmlspecialchars($opcion) ?>" style="font-size: 1.2em; color: #333;">
                            <?= htmlspecialchars($opcion) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="contestar" class="btn btn-success btn-lg mt-3" style="width: 100%;">Contestar</button>
            </form>
        </div>
        
        <form method="POST" class="text-center mt-3">
            <button type="submit" name="terminar" class="btn btn-danger">Terminar Juego</button>
        </form>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-4 py-3">
        <p class="mb-1">Proyecto realizado por <strong>Raulito</strong></p>
        <p class="mb-0">Datos obtenidos de <a href="https://www.un.org/development/desa/en/" target="_blank">ONU</a> para el año 2024</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themes = {
                'solar': 'css/themes/solar.min.css',
                'slate': 'css/themes/slate.min.css',
                'sketchy': 'css/themes/sketchy.min.css',
                'quartz': 'css/themes/quartz.min.css'
            };

            const lightThemes = ['solar', 'slate', 'quartz'];
            const select = document.getElementById('tema');
            const themeLink = document.getElementById('theme-link');

            const savedTheme = localStorage.getItem('selectedTheme') || 'quartz';
            if (savedTheme && themes[savedTheme]) {
                select.value = savedTheme;
                themeLink.href = themes[savedTheme];
                if (lightThemes.includes(savedTheme)) {
                    document.body.classList.add('theme-light-icon');
                }
            } else {
                themeLink.href = themes['quartz'];
                select.value = 'quartz';
                document.body.classList.add('theme-light-icon');
            }

            select.addEventListener('change', function() {
                const theme = this.value;
                localStorage.setItem('selectedTheme', theme);
                themeLink.href = themes[theme];

                if (lightThemes.includes(theme)) {
                    document.body.classList.add('theme-light-icon');
                } else {
                    document.body.classList.remove('theme-light-icon');
                }
            });
        });
    </script>
</body>
</html>

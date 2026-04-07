<?php
function conectarBD() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'paises';

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function validarUrl($url) {
    if (empty($url)) return '';
    if (strpos($url, 'https://flagpedia.net/data/flags') !== 0) return '';
    
    try {
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $testContent = @file_get_contents($url, false, $context);
        if ($testContent !== false && strpos($testContent, '<') !== false && 
            (stripos($testContent, 'Warning') !== false || stripos($testContent, 'Error') !== false || 
             stripos($testContent, 'Undefined') !== false || stripos($testContent, 'Notice') !== false)) {
            return '';
        }
        return $url;
    } catch (Exception $e) {}
    return '';
}

function validarEntero($valor) {
    return filter_var($valor, FILTER_VALIDATE_INT);
}

function obtenerBadgeClass($continente) {
    $badges = [
        'Europa' => 'badge-europa',
        'Asia' => 'badge-asia',
        'América del Norte' => 'badge-america-norte',
        'América del Sur' => 'badge-america-sur',
        'África' => 'badge-africa',
        'Oceanía' => 'badge-oceania'
    ];
    return $badges[$continente] ?? 'bg-primary';
}

function formatearFecha($fecha) {
    return $fecha ? date('d/m/Y', strtotime($fecha)) : '-';
}

function formatearNumero($numero) {
    return number_format($numero, 0, ',', '.');
}
?>
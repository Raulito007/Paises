<?php
function cargarPaisParaEdicion($conn, $nombre) {
    $stmt = $conn->prepare("SELECT * FROM paises_del_mundo WHERE nombre_del_pais=?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!isset($row['bandera'])) {
            $row['bandera'] = '';
        }
        if (!isset($row['Wiki_capital'])) {
            $row['Wiki_capital'] = '';
        }
        return $row;
    }
    return null;
}

function obtenerTotalPibYEmisiones($conn) {
    $totales = $conn->query("SELECT SUM(pib_2024_usd) as total_pib, SUM(emisiones_co2_2024_toneladas) as total_emisiones FROM paises_del_mundo")->fetch_assoc();
    return [
        'pib' => $totales['total_pib'] ?: 1,
        'emisiones' => $totales['total_emisiones'] ?: 1
    ];
}

function obtenerPaises($conn, $search = '', $sortColumn = 'nombre_del_pais', $sortDir = 'ASC') {
    $allowedColumns = ['nombre_del_pais', 'capital', 'continente', 'habitantes', 'superficie', 'pib_2024_usd', 'emisiones_co2_2024_toneladas', 'fecha_admision_onu'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'nombre_del_pais';
    }

    $sql = "SELECT * FROM paises_del_mundo";
    $params = [];
    $types = "";

    if ($search !== '') {
        $sql .= " WHERE nombre_del_pais LIKE ? OR capital LIKE ? OR continente LIKE ?";
        $searchParam = "%" . $search . "%";
        $params = [$searchParam, $searchParam, $searchParam];
        $types = "sss";
    }

    $sql .= " ORDER BY " . $sortColumn . " " . $sortDir;

    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    } else {
        return $conn->query($sql);
    }
}

function crearPais($conn, $datos) {
    $stmt = $conn->prepare("INSERT INTO paises_del_mundo (nombre_del_pais, continente, capital, Wiki_capital, habitantes, superficie, pib_2024_usd, emisiones_co2_2024_toneladas, bandera) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiiss", $datos['nombre'], $datos['continente'], $datos['capital'], $datos['wiki_capital'], $datos['habitantes'], $datos['superficie'], $datos['pib'], $datos['emisiones'], $datos['bandera']);
    return $stmt->execute() ? '' : "Error {$stmt->errno}: {$stmt->error}";
}

function actualizarPais($conn, $datos, $nombreOriginal) {
    $stmt = $conn->prepare("UPDATE paises_del_mundo SET nombre_del_pais=?, bandera=?, Wiki_capital=?, capital=?, continente=?, habitantes=?, superficie=?, pib_2024_usd=?, emisiones_co2_2024_toneladas=? WHERE nombre_del_pais=?");
    $stmt->bind_param("ssssiiiiss", $datos['nombre'], $datos['bandera'], $datos['wiki_capital'], $datos['capital'], $datos['continente'], $datos['habitantes'], $datos['superficie'], $datos['pib'], $datos['emisiones'], $nombreOriginal);
    return $stmt->execute() ? '' : "Error {$stmt->errno}: {$stmt->error}";
}

function eliminarPais($conn, $nombre) {
    $stmt = $conn->prepare("DELETE FROM paises_del_mundo WHERE nombre_del_pais=?");
    $stmt->bind_param("s", $nombre);
    return $stmt->execute() ? '' : "Error {$stmt->errno}: {$stmt->error}";
}
?>
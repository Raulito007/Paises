<?php
require_once 'php/funciones.php';
require_once 'php/operaciones.php';

$conn = conectarBD();

$message = '';
$country = [
    'nombre_del_pais' => '', 'capital' => '', 'continente' => '', 
    'habitantes' => '', 'superficie' => '', 'pib_2024_usd' => '', 
    'emisiones_co2_2024_toneladas' => '', 'bandera' => '', 
    'Wikipedia' => '', 'Wiki_capital' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $datos = [
        'nombre' => $_POST['nombre_del_pais'],
        'continente' => $_POST['continente'],
        'capital' => $_POST['capital'],
        'wiki_capital' => $_POST['wiki_capital'] ?? '',
        'habitantes' => validarEntero($_POST['habitantes']),
        'superficie' => validarEntero($_POST['superficie']),
        'pib' => validarEntero($_POST['pib_2024_usd']),
        'emisiones' => validarEntero($_POST['emisiones_co2_2024_toneladas']),
        'bandera' => validarUrl($_POST['banderas'])
    ];

    if ($datos['habitantes'] === false || $datos['superficie'] === false || $datos['pib'] === false || $datos['emisiones'] === false) {
        $message = 'Los campos numéricos deben ser enteros válidos';
    } else {
        if ($_POST['action'] === 'create') {
            $message = crearPais($conn, $datos);
            if (!$message) {
                header('Location: index.php');
                exit;
            }
        } elseif ($_POST['action'] === 'update') {
            $message = actualizarPais($conn, $datos, $_POST['nombre_original']);
            if (!$message) {
                header('Location: index.php');
                exit;
            }
        } elseif ($_POST['action'] === 'delete') {
            $message = eliminarPais($conn, $_POST['nombre_original']);
            if (!$message) {
                header('Location: index.php');
                exit;
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $country = cargarPaisParaEdicion($conn, $_GET['edit']) ?: $country;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'nombre_del_pais';
$sortDir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

$result = obtenerPaises($conn, $search, $sortColumn, $sortDir);
$paises_filtrados = $result->fetch_all(MYSQLI_ASSOC);
$totalPaises = count($paises_filtrados);

$totales = obtenerTotalPibYEmisiones($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Países del Mundo</title>
    <link rel="icon" href="icons/globo-terraqueo.png" type="image/png">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/themes/quartz.min.css" id="theme-link">
</head>
<body class="bg-light" style="background-color: #6c757d;">
    <div class="container-fluid py-4">
        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0"><img src="icons/bola.png" alt="" class="bola-titulo">Países del Mundo <small class="text-muted">(<?= $totalPaises ?> países)</small></h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="?create=1" class="btn btn-success">Agregar País</a>
                <a href="juego.php?nuevo=1" class="btn btn-warning">🎮 Juego</a>
                <a href="juegoContinente.php?nuevo=1" class="btn btn-warning">🌍 Continentes</a>
                <a href="juegoBandera.php?nuevo=1" class="btn btn-warning">🏳️ Banderas</a>
                <label for="tema" class="me-2 fw-bold">TEMA:</label>
                <select id="tema" class="form-select" style="width: auto;">
                    <option value="quartz" selected>Quartz</option>
                    <option value="solar">Solar</option>
                    <option value="slate">Slate</option>
                    <option value="sketchy">Sketchy</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <form method="GET" class="mb-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar por país, capital o continente..." value="<?= htmlspecialchars($search) ?>">
            </form>
            <table class="table table-bordered table-striped table-hover border-dark" id="tablaPaises" style="min-width: 1620px;">
                <thead class="table-dark">
                    <tr>
                        <th><a href="?sort=nombre_del_pais&dir=<?= $sortColumn === 'nombre_del_pais' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">País <?= $sortColumn === 'nombre_del_pais' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=capital&dir=<?= $sortColumn === 'capital' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Capital <?= $sortColumn === 'capital' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=continente&dir=<?= $sortColumn === 'continente' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Continente <?= $sortColumn === 'continente' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=habitantes&dir=<?= $sortColumn === 'habitantes' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Habitantes <?= $sortColumn === 'habitantes' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=superficie&dir=<?= $sortColumn === 'superficie' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Superficie <?= $sortColumn === 'superficie' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=pib_2024_usd&dir=<?= $sortColumn === 'pib_2024_usd' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">PIB <?= $sortColumn === 'pib_2024_usd' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=pib_2024_usd&dir=<?= $sortColumn === 'pib_2024_usd' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">% PIB Mundial <?= $sortColumn === 'pib_2024_usd' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=emisiones_co2_2024_toneladas&dir=<?= $sortColumn === 'emisiones_co2_2024_toneladas' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">CO₂ <?= $sortColumn === 'emisiones_co2_2024_toneladas' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th><a href="?sort=emisiones_co2_2024_toneladas&dir=<?= $sortColumn === 'emisiones_co2_2024_toneladas' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">% Emisiones Mundial <?= $sortColumn === 'emisiones_co2_2024_toneladas' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th>Bandera</th>
                        <th><a href="?sort=fecha_admision_onu&dir=<?= $sortColumn === 'fecha_admision_onu' && $sortDir === 'ASC' ? 'desc' : 'asc' ?><?= $search ? '&search=' . urlencode($search) : '' ?>">ONU <?= $sortColumn === 'fecha_admision_onu' ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?></a></th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paises_filtrados as $row): ?>
                        <?php 
                        $flagUrl = $row['bandera'];
                        $showFlag = $flagUrl && (strpos($flagUrl, 'http://') === 0 || strpos($flagUrl, 'https://') === 0);
                        $wikipediaUrl = $row['Wikipedia'];
                        $showWiki = $wikipediaUrl && strpos($wikipediaUrl, '<') === false;
                        $wikiCapitalUrl = $row['Wiki_capital'] ?? '';
                        $showWikiCapital = $wikiCapitalUrl && strpos($wikiCapitalUrl, '<') === false;
                        $fechaOnuFormat = formatearFecha($row['fecha_admision_onu']);
                        $badgeClass = obtenerBadgeClass($row['continente']);
                        ?>
                        <tr>
                            <td>
                                <strong><?= $showWiki ? '<a href="' . htmlspecialchars($wikipediaUrl) . '" target="_blank" title="Ver en Wikipedia">' . htmlspecialchars($row['nombre_del_pais']) . '</a>' : htmlspecialchars($row['nombre_del_pais']) ?></strong>
                            </td>
                            <td><?= $showWikiCapital ? '<a href="' . htmlspecialchars($wikiCapitalUrl) . '" target="_blank" title="Ver capital en Wikipedia">' . htmlspecialchars($row['capital']) . '</a>' : htmlspecialchars($row['capital']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($row['continente']) ?></span></td>
                            <td><?= formatearNumero($row['habitantes']) ?></td>
                            <td><?= formatearNumero($row['superficie']) ?></td>
                            <td>$<?= formatearNumero($row['pib_2024_usd']) ?></td>
                            <td><?= number_format(($row['pib_2024_usd'] / $totales['pib']) * 100, 2, ',', '.') ?>%</td>
                            <td><?= formatearNumero($row['emisiones_co2_2024_toneladas']) ?></td>
                            <td><?= number_format(($row['emisiones_co2_2024_toneladas'] / $totales['emisiones']) * 100, 2, ',', '.') ?>%</td>
                            <td><?php if ($showFlag): ?><div id="contenedor-bandera"><img src="<?= htmlspecialchars($flagUrl) ?>" alt="Bandera" style="width: 50px; height: auto;"></div><?php else: ?><span class="text-muted">NO DISPONIBLE</span><?php endif; ?></td>
                            <td><?= $fechaOnuFormat ?></td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="?edit=<?= urlencode($row['nombre_del_pais']) ?>" class="btn btn-sm btn-primary me-1" title="Editar">✏️</a>
                                <button class="btn btn-sm btn-danger me-1" title="Eliminar" onclick="confirmDelete('<?= htmlspecialchars($row['nombre_del_pais']) ?>')">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($country['nombre_del_pais'] || isset($_GET['create'])): ?>
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= isset($_GET['create']) ? 'Agregar País' : 'Editar País' ?></h5>
                </div>
                <form method="POST" class="modal-body">
                    <input type="hidden" name="action" value="<?= isset($_GET['create']) ? 'create' : 'update' ?>">
                    <?php if (isset($_GET['edit'])): ?>
                    <input type="hidden" name="nombre_original" value="<?= htmlspecialchars($country['nombre_del_pais']) ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre del País</label>
                        <input type="text" name="nombre_del_pais" class="form-control" value="<?= htmlspecialchars($country['nombre_del_pais']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bandera (URL)</label>
                        <input type="text" name="banderas" class="form-control" value="<?= htmlspecialchars($country['bandera'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capital</label>
                        <input type="text" name="capital" class="form-control" value="<?= htmlspecialchars($country['capital']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wiki Capital (URL)</label>
                        <input type="text" name="wiki_capital" class="form-control" value="<?= htmlspecialchars($country['Wiki_capital'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Continente</label>
                        <select name="continente" class="form-select" required>
                            <option value="Europa" <?= $country['continente'] === 'Europa' ? 'selected' : '' ?>>Europa</option>
                            <option value="Asia" <?= $country['continente'] === 'Asia' ? 'selected' : '' ?>>Asia</option>
                            <option value="América del Norte" <?= $country['continente'] === 'América del Norte' ? 'selected' : '' ?>>América del Norte</option>
                            <option value="América del Sur" <?= $country['continente'] === 'América del Sur' ? 'selected' : '' ?>>América del Sur</option>
                            <option value="África" <?= $country['continente'] === 'África' ? 'selected' : '' ?>>África</option>
                            <option value="Oceanía" <?= $country['continente'] === 'Oceanía' ? 'selected' : '' ?>>Oceanía</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Habitantes</label>
                        <input type="number" name="habitantes" step="1" class="form-control" value="<?= htmlspecialchars($country['habitantes']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Superficie (km²)</label>
                        <input type="number" name="superficie" step="1" class="form-control" value="<?= htmlspecialchars($country['superficie']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIB 2024 (USD)</label>
                        <input type="number" name="pib_2024_usd" step="1" class="form-control" value="<?= htmlspecialchars($country['pib_2024_usd']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Emisiones CO₂ 2024 (toneladas)</label>
                        <input type="number" name="emisiones_co2_2024_toneladas" step="1" class="form-control" value="<?= htmlspecialchars($country['emisiones_co2_2024_toneladas']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="text-center mt-4 py-3">
        <p class="mb-1">Proyecto realizado por <strong>Raulito</strong></p>
        <p class="mb-0">Datos obtenidos de <a href="https://www.un.org/development/desa/en/" target="_blank">ONU</a> para el año 2024</p>
    </footer>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="nombre_original" id="deleteId">
    </form>

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>

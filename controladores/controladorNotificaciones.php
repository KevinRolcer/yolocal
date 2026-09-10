<?php
if (!function_exists('dbConectar')) {
    require_once __DIR__ . '/../config.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function inicializarTablaNotificaciones(): bool
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $conexion->query("CREATE TABLE IF NOT EXISTS notificaciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        mensaje TEXT NOT NULL,
        tipo VARCHAR(50) NOT NULL DEFAULT 'info',
        leida TINYINT(1) NOT NULL DEFAULT 0,
        url_accion VARCHAR(255) DEFAULT NULL,
        fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    return true;
}

function obtenerColumnaUsuarioNotificaciones(mysqli $conexion): string
{
    static $columnaUsuario = null;

    if ($columnaUsuario !== null) {
        return $columnaUsuario;
    }

    $resultado = $conexion->query("SHOW COLUMNS FROM notificaciones LIKE 'usuario_id'");
    if ($resultado && $resultado->num_rows > 0) {
        $columnaUsuario = 'usuario_id';
        return $columnaUsuario;
    }

    $resultado = $conexion->query("SHOW COLUMNS FROM notificaciones LIKE 'ID_Usuario'");
    if ($resultado && $resultado->num_rows > 0) {
        $columnaUsuario = 'ID_Usuario';
        return $columnaUsuario;
    }

    // Fallback seguro para esquemas incompletos.
    $conexion->query("ALTER TABLE notificaciones ADD COLUMN usuario_id INT NOT NULL AFTER id");
    $columnaUsuario = 'usuario_id';
    return $columnaUsuario;
}

function crearNotificacion(int $usuarioId, string $titulo, string $mensaje, string $tipo = 'info', ?string $urlAccion = null): bool
{
    inicializarTablaNotificaciones();

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $columnaUsuario = obtenerColumnaUsuarioNotificaciones($conexion);
    $stmt = $conexion->prepare("INSERT INTO notificaciones ({$columnaUsuario}, titulo, mensaje, tipo, url_accion) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('issss', $usuarioId, $titulo, $mensaje, $tipo, $urlAccion);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function obtenerNotificacionesUsuario(int $usuarioId, int $limite = 50): array
{
    inicializarTablaNotificaciones();

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return [];
    }

    $columnaUsuario = obtenerColumnaUsuarioNotificaciones($conexion);
    $stmt = $conexion->prepare("SELECT id, titulo, mensaje, tipo, leida, url_accion, fecha_creacion FROM notificaciones WHERE {$columnaUsuario} = ? ORDER BY fecha_creacion DESC LIMIT ?");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('ii', $usuarioId, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }

    $stmt->close();
    return $datos;
}

function contarNotificacionesNoLeidas(int $usuarioId): int
{
    inicializarTablaNotificaciones();

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return 0;
    }

    $columnaUsuario = obtenerColumnaUsuarioNotificaciones($conexion);
    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE {$columnaUsuario} = ? AND leida = 0");
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();

    return (int)($fila['total'] ?? 0);
}

function marcarNotificacionLeida(int $id): bool
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $stmt = $conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function marcarTodasNotificacionesLeidas(int $usuarioId): bool
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $columnaUsuario = obtenerColumnaUsuarioNotificaciones($conexion);
    $stmt = $conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE {$columnaUsuario} = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $usuarioId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

if ((($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && !headers_sent()) {
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        $accion = $_POST['action'];

        if ($accion === 'marcar_leida' && isset($_POST['id'])) {
            echo json_encode(['ok' => marcarNotificacionLeida((int)$_POST['id'])]);
            exit;
        }

        if ($accion === 'marcar_todo_leido' && isset($_SESSION['ID_Usuario'])) {
            echo json_encode(['ok' => marcarTodasNotificacionesLeidas((int)$_SESSION['ID_Usuario'])]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Acci¨®n no soportada']);
        exit;
    }
}

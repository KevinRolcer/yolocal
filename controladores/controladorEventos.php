<?php
/* Evita que avisos HTML rompan la respuesta JSON del API */
ini_set('display_errors', '0');
error_reporting(E_ALL);
include_once("../config.php");

require_once '../modelos/eventos.php';

/**
 * Asegura columna opcional Teléfono en `eventos` (muchas BD antiguas no la tienen).
 */
function yl_eventos_ensure_telefono_column(mysqli $db): void
{
    static $already = false;
    if ($already) {
        return;
    }
    $already = true;
    $res = mysqli_query($db, "SHOW COLUMNS FROM `eventos`");
    if (!$res) {
        return;
    }
    while ($row = mysqli_fetch_assoc($res)) {
        if (isset($row['Field']) && strcasecmp((string) $row['Field'], 'Telefono') === 0) {
            mysqli_free_result($res);
            return;
        }
    }
    mysqli_free_result($res);

    $alters = [
        "ALTER TABLE `eventos` ADD COLUMN `Telefono` VARCHAR(15) NULL DEFAULT NULL AFTER `UbicacionE`",
        "ALTER TABLE `eventos` ADD COLUMN `Telefono` VARCHAR(15) NULL DEFAULT NULL",
    ];
    foreach ($alters as $sql) {
        if (mysqli_query($db, $sql)) {
            return;
        }
        if (mysqli_errno($db) === 1060) {
            /* Duplicate column (carrera u otra petición) */
            return;
        }
    }
    error_log('[controladorEventos] ALTER Telefono falló: ' . mysqli_error($db));
}

header('Content-Type: application/json; charset=utf-8');
$respuesta = ['success' => false, 'message' => 'Operación no reconocida.'];

$conexion = dbConectar();
if (!($conexion instanceof mysqli)) {
    $respuesta['message'] = 'Error de conexión a la base de datos: '
        . (is_string($conexion) ? $conexion : mysqli_connect_error());
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

yl_eventos_ensure_telefono_column($conexion);

$modeloEventos = new ModeloEventos($conexion);
$operacion = $_POST['ope'] ?? null;

try {

switch ($operacion) {
    case 'CARGAR_CATEGORIAS':
        $respuesta['lista'] = $modeloEventos->listarCategorias();
        $respuesta['success'] = true;
        break;

    case 'LISTAR':
        $respuesta['lista'] = $modeloEventos->listarEventos(trim($_POST['buscar'] ?? ''));
        $respuesta['success'] = true;
        break;

    case 'AGREGAR':
        $nombreImagen = null;
        if (isset($_FILES['RutaImagenE']) && $_FILES['RutaImagenE']['error'] === UPLOAD_ERR_OK) {
            $directorioImagenes = '../imagenes/'; // Carpeta para guardar las imágenes
            if (!file_exists($directorioImagenes)) {
                mkdir($directorioImagenes, 0777, true);
            }
            $extension = pathinfo($_FILES['RutaImagenE']['name'], PATHINFO_EXTENSION);
            $nombreImagen = uniqid('evento_') . '.' . $extension;
            move_uploaded_file($_FILES['RutaImagenE']['tmp_name'], $directorioImagenes . $nombreImagen);
        }
        
    
        $resultado = $modeloEventos->agregarEvento(
            $_POST['TituloE'] ?? '',
            $_POST['DescripcionE'] ?? '',
            $_POST['PrecioE'] ?? '',
            $_POST['FechaE'] ?? '',
            $_POST['HoraE'] ?? '',
            $_POST['UbicacionE'] ?? '',
            $_POST['Telefono'] ?? '',
            $nombreImagen,
            $_POST['ID_Categoria'] ?? ''
        );

        if ($resultado) {
            $respuesta['success'] = true;
            $respuesta['message'] = 'Evento agregado con éxito.';
        } else {
            $respuesta['message'] = 'Error al agregar el evento.';
        }
        break;
        
    case 'OBTENER':
        $evento = $modeloEventos->obtenerEventoPorId((int)($_POST['ID_Evento'] ?? 0));
        if ($evento) {
            $respuesta['success'] = true;
            $respuesta['evento'] = $evento;
        } // Se podría añadir un 'else' para mensaje de error si no se encuentra
        break;

    case 'EDITAR':
        $nombreImagen = null;
        $idEditar = (int)($_POST['ID_Evento'] ?? 0);
        // Lógica para manejar nueva imagen
        if (isset($_FILES['RutaImagenE']) && $_FILES['RutaImagenE']['error'] === UPLOAD_ERR_OK) {
            // (Opcional) Borrar imagen anterior si existe
            $eventoActual = $modeloEventos->obtenerEventoPorId($idEditar);
            if ($eventoActual && !empty($eventoActual['RutaImagenE'])) {
                @unlink('../imagenes/' . $eventoActual['RutaImagenE']);
            }
            
            $directorioImagenes = '../imagenes/';
            $extension = pathinfo($_FILES['RutaImagenE']['name'], PATHINFO_EXTENSION);
            $nombreImagen = uniqid('evento_') . '.' . $extension;
            move_uploaded_file($_FILES['RutaImagenE']['tmp_name'], $directorioImagenes . $nombreImagen);
        }

        $resultado = $modeloEventos->editarEvento(
            $idEditar,
            $_POST['TituloE'] ?? '',
            $_POST['DescripcionE'] ?? '',
            $_POST['PrecioE'] ?? '',
            $_POST['FechaE'] ?? '',
            $_POST['HoraE'] ?? '',
            $_POST['UbicacionE'] ?? '',
            $_POST['Telefono'] ?? '',
            $nombreImagen,
            (int)($_POST['ID_Categoria'] ?? 0)
        );
        if ($resultado) {
            $respuesta['success'] = true;
              $respuesta['message'] = 'Evento actualizado con éxito.';
        } else {
              $respuesta['message'] = 'Error al actualizar el evento.';
        }
        break;

    case 'ELIMINAR':
        $idEliminar = (int)($_POST['ID_Evento'] ?? 0);
        $evento = $modeloEventos->obtenerEventoPorId($idEliminar);

        $resultado = $modeloEventos->eliminarEvento($idEliminar);
        if ($resultado) {
            if ($evento && !empty($evento['RutaImagenE'])) {
                @unlink('../imagenes/' . $evento['RutaImagenE']); // El @ suprime errores si el archivo no existe
            }
            $respuesta['success'] = true;
             // Podrías añadir un mensaje de éxito aquí si quieres
              $respuesta['message'] = 'Evento eliminado con éxito.';
        } else {
             // Podrías añadir un mensaje de error aquí si quieres
              $respuesta['message'] = 'Error al eliminar el evento.';
        }
        break;
}

} catch (Throwable $e) {
    $respuesta = [
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage(),
    ];
}

$conexion->close();

$flags = JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;
if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
    $flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
}
$json = json_encode($respuesta, $flags);
if ($json === false) {
    $json = json_encode(
        ['success' => false, 'message' => 'Error al codificar respuesta JSON.'],
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
}
echo $json;

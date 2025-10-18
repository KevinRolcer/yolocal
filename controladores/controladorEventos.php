<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once("../config.php");

require_once '../modelos/eventos.php'; 

header('Content-Type: application/json');
$respuesta = ['success' => false, 'message' => 'Operación no reconocida.'];


$conexion = dbConectar(); 
if ($conexion === false) {
    $respuesta['message'] = 'Error de conexión a la base de datos: ' . mysqli_connect_error();
    echo json_encode($respuesta);
    exit;
}
$modeloEventos = new ModeloEventos($conexion);
$operacion = $_POST['ope'] ?? null;

switch ($operacion) {
    case 'CARGAR_CATEGORIAS':
        $respuesta['lista'] = $modeloEventos->listarCategorias();
        $respuesta['success'] = true;
        break;

    case 'LISTAR':
        $respuesta['lista'] = $modeloEventos->listarEventos();
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
            $_POST['TituloE'], 
            $_POST['DescripcionE'], 
            $_POST['PrecioE'], 
            $_POST['FechaE'],
            $_POST['HoraE'], 
            $_POST['UbicacionE'], 
            $_POST['Telefono'], 
            $nombreImagen, 
            $_POST['ID_Categoria']
        );

        if ($resultado) {
            $respuesta['success'] = true;
            $respuesta['message'] = 'Evento agregado con éxito.';
        } else {
            $respuesta['message'] = 'Error al agregar el evento.';
        }
        break;
        
    case 'OBTENER':
        $evento = $modeloEventos->obtenerEventoPorId($_POST['ID_Evento']);
        if ($evento) {
            $respuesta['success'] = true;
            $respuesta['evento'] = $evento;
        } // Se podría añadir un 'else' para mensaje de error si no se encuentra
        break;

    case 'EDITAR':
        $nombreImagen = null;
        // Lógica para manejar nueva imagen
        if (isset($_FILES['RutaImagenE']) && $_FILES['RutaImagenE']['error'] === UPLOAD_ERR_OK) {
            // (Opcional) Borrar imagen anterior si existe
            $eventoActual = $modeloEventos->obtenerEventoPorId($_POST['ID_Evento']);
            if ($eventoActual && !empty($eventoActual['RutaImagenE'])) {
                @unlink('../imagenes/' . $eventoActual['RutaImagenE']);
            }
            
            $directorioImagenes = '../imagenes/';
            $extension = pathinfo($_FILES['RutaImagenE']['name'], PATHINFO_EXTENSION);
            $nombreImagen = uniqid('evento_') . '.' . $extension;
            move_uploaded_file($_FILES['RutaImagenE']['tmp_name'], $directorioImagenes . $nombreImagen);
        }

        // Se añade $_POST['Telefono'] en la posición correcta
        $resultado = $modeloEventos->editarEvento(
            $_POST['ID_Evento'], 
            $_POST['TituloE'], 
            $_POST['DescripcionE'], 
            $_POST['PrecioE'],
            $_POST['FechaE'], 
            $_POST['HoraE'], 
            $_POST['UbicacionE'], 
            $_POST['Telefono'], // <--- CAMPO AÑADIDO AQUÍ
            $nombreImagen, 
            $_POST['ID_Categoria']
        );
        if ($resultado) {
            $respuesta['success'] = true;
              $respuesta['message'] = 'Evento actualizado con éxito.';
        } else {
              $respuesta['message'] = 'Error al actualizar el evento.';
        }
        break;

    case 'ELIMINAR':
        // Antes de eliminar de la BD, obtenemos el nombre de la imagen para borrar el archivo
        $evento = $modeloEventos->obtenerEventoPorId($_POST['ID_Evento']);
        
        $resultado = $modeloEventos->eliminarEvento($_POST['ID_Evento']);
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

$conexion->close();
echo json_encode($respuesta);
?>
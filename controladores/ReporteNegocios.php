<?php
// controllers/MiembroController.php
require_once '../config.php';
require_once '../modelos/ReporteNegocios.php';
require_once '../lib/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
$conexion = dbConectar();
$controlador = new MiembroController($conexion);
$controlador->generarReporteExcel();
class MiembroController {
    private $modelo;

    public function __construct($conexion) {
        $this->modelo = new MiembroModel($conexion);
    }


    public function generarReporteExcel() {
        ob_start();
        $miembros = $this->modelo->obtenerNegocios();
       
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

      

        // Encabezados
        $encabezados = ['Nombre Negocio', 'Telefono', 'Correo', 'Dueño', 'Categoria', 'Fecha Registro', 'Estado'];
        $sheet->fromArray($encabezados, NULL, 'A1');

        // Datos
        $fila = 2;
        foreach ($miembros as $row) {
            $sheet->setCellValue("A$fila", $row['nombre_negocio']);
            $sheet->setCellValue("B$fila", $row['Telefono']);
            $sheet->setCellValue("C$fila", $row['CorreoN']);
            $sheet->setCellValue("D$fila", $row['Nombre'] . ' ' . $row['ApellidoP'] . ' ' . $row['ApellidoM']);
            $sheet->setCellValue("E$fila", $row['Descripcion']);
            $sheet->setCellValue("F$fila", $row['fecha_registro']);
            $sheet->setCellValue("G$fila", $row['estado']);
            $fila++;
        }
        ob_clean();
        // Enviar encabezados
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="ReporteNegocios.xlsx"');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
?>
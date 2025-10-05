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
   
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $encabezados = ['Nombre Negocio', 'Telefono', 'Correo', 'Dueño', 'Categoria', 'Fecha Registro', 'Último Pago', 'Próximo Pago', 'Estado'];
    $sheet->fromArray($encabezados, NULL, 'A1');

    $fila = 2;
    $hoy = new DateTime();

    foreach ($miembros as $row) {
        $fechaRegistro = new DateTime($row['fecha_registro']);
        $fechaUltimoPago = !empty($row['fecha_ultimo_pago'])
            ? new DateTime($row['fecha_ultimo_pago'])
            : clone $fechaRegistro; // si nunca ha pagado, usamos registro

        // Próximo pago = último pago + 1 mes
        $fechaProximoPago = clone $fechaUltimoPago;
        $fechaProximoPago->modify('+1 month');

        // Calcular diferencia
        $diff = (int)$hoy->diff($fechaProximoPago)->format('%r%a');

        // Datos normales
        $sheet->setCellValue("A$fila", $row['nombre_negocio']);
        $sheet->setCellValue("B$fila", $row['Telefono']);
        $sheet->setCellValue("C$fila", $row['CorreoN']);
        $sheet->setCellValue("D$fila", $row['Nombre'] . ' ' . $row['ApellidoP'] . ' ' . $row['ApellidoM']);
        $sheet->setCellValue("E$fila", $row['Descripcion']);
        $sheet->setCellValue("F$fila", $row['fecha_registro']);
        $sheet->setCellValue("G$fila", $fechaUltimoPago->format('Y-m-d'));
        $sheet->setCellValue("H$fila", $fechaProximoPago->format('Y-m-d'));
        $sheet->setCellValue("I$fila", $row['estado']);

        // --- Semaforización ---
        $color = '00FF00'; // Verde
        if ($diff < 0) {
            $color = 'FF0000'; // Rojo (ya venció)
        } elseif ($diff <= 5) {
            $color = 'FFFF00'; // Amarillo (faltan 5 o menos)
        }

        $sheet->getStyle("A$fila:I$fila")->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB($color);

        $fila++;
    }

    ob_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="ReporteNegocios.xlsx"');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
}

}
?>
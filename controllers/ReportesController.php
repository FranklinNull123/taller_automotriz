<?php
require_once '../models/ReporteModel.php';
require_once '../vendor/autoload.inc.php'; // Cargar Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'generar_pdf':
        // Habilitar carga de estilos e instanciar Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Diseñar la plantilla HTML (puedes usar tu propia estructura)
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Helvetica, sans-serif; color: #333; }
                .cabecera { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 20px; }
                h2 { color: #1e293b; margin: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
                th { background-color: #f8f9fa; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="cabecera">
                <h2>REPORTE DE PRODUCTIVIDAD</h2>
                <p>Taller Automotriz - ' . date('d/m/Y') . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Órdenes Atendidas (Mes)</td>
                        <td>Consulta en base de datos</td>
                    </tr>
                    <tr>
                        <td>Vehículos en Taller</td>
                        <td>Consulta en base de datos</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
        ';

        // Procesar y renderizar el documento
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // El parámetro Attachment => false lo abre en el navegador sin descargarlo forzosamente
        $dompdf->stream("Reporte_Productividad.pdf", ["Attachment" => false]); 
        break;

    case 'exportar_excel':
        break;
}
?>
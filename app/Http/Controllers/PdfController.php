<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF; // alias de dompdf

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        $view = $request->view; // ej: 'reports.detailed'
        $data = unserialize(base64_decode($request->data));

        // Renderizamos la vista con los datos
        $pdf = PDF::loadView($view, $data);

        // Configuración para A4 vertical
        $pdf->setPaper('A4', 'landscape');

        // Nombre del archivo
        $filename = 'reporte_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
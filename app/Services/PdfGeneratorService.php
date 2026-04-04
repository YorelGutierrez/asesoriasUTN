<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorService
{
    public function generarAsesoriaPdf($data)
    {
        $pdf = Pdf::loadView('pdf.asesoria', ['data' => $data]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf;
    }

    public function descargarAsesoriaPdf($data, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'asesoria_' . date('Ymd_His') . '.pdf';
        $pdf = $this->generarAsesoriaPdf($data);
        return $pdf->download($nombreArchivo);
    }
}
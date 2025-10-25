<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    /**
     * Renderiza una vista Blade a PDF y devuelve los bytes.
     */
    public function renderView(string $view, array $data = [], string $paper = 'letter', string $orientation = 'portrait'): string
    {
        $html = view($view, $data)->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);      // permite data URIs y recursos remotos
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans'); // soporta tildes/UTF-8

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf->output(); // bytes del PDF
    }
}
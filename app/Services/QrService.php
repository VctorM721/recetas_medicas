<?php
namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrService {
    // PNG para PDF
    public function dataUri(string $text, int $size=200): string{
        $opts = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale'      => max(1,(int)($size/40)),
            'margin'     => 1,
        ]);
        $png = (new QRCode($opts))->render($text);
        return 'data:image/png;base64,'.base64_encode($png);
    }

    // SVG INLINE para web (¡NO data URI!)
    public function svg(string $text, int $size=200): string{
        $opts = new QROptions([
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'scale'        => max(1,(int)($size/40)),
            'margin'       => 1,
            'outputBase64' => false, // fuerza markup <svg>, no data:
        ]);
        return (new QRCode($opts))->render($text); // <-- Devuelve "<svg ...>...</svg>"
    }
}
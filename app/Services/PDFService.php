<?php

namespace App\Services;

use App\Models\Demande;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;


class PDFService
{
    public static function generateQRCode(string $reference): string
    {
        return base64_encode(QrCode::format('png')->size(150)->generate($reference));
    }

    public static function generateDemandePDF(Demande $demande)
    {
        return Pdf::loadView('pdf.demande', ['demande' => $demande]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payer(Document $document)
    {
        // Vérifie que le document est bien approuvé
        if ($document->status !== \App\Enums\DocumentStatus::APPROUVEE) {
            return back()->with('error', 'Le document n’est pas encore approuvé.');
        }

        // Simuler le paiement (tu peux remplacer par Stripe, etc.)
        $document->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return redirect()->route('citoyen.demandes.show', $document)
                         ->with('success', 'Paiement effectué avec succès.');
    }
}

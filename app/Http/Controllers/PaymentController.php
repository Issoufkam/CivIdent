<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Affiche le formulaire de paiement pour un document
     */
    public function showPaymentForm(Document $document)
    {
        // Vérifier que le document appartient à l'utilisateur connecté
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le document est approuvé et non payé
        if ($document->status !== \App\Enums\DocumentStatus::APPROUVEE || $document->is_paid) {
            return redirect()->back()->with('error', 'Ce document ne peut pas être payé');
        }

        return view('citoyen.paiements.paiement', [
            'document' => $document,
            'montant' => $this->calculateAmount($document->type)
        ]);
    }

    /**
     * Traite le paiement
     */
    public function processPayment(Request $request, Document $document)
    {
        // Validation
        $validated = $request->validate([
            'payment_method' => 'required|in:card,mobile_money',
            'card_number' => 'required_if:payment_method,card',
            'expiry_date' => 'required_if:payment_method,card',
            'cvv' => 'required_if:payment_method,card',
            'mobile_operator' => 'required_if:payment_method,mobile_money',
            'phone_number' => 'required_if:payment_method,mobile_money'
        ]);

        // Enregistrement du paiement
        $payment = Payment::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'amount' => $this->calculateAmount($document->type),
            'payment_method' => $validated['payment_method'],
            'transaction_id' => 'TRX-' . strtoupper(uniqid()),
            'status' => 'completed'
        ]);

        // Mise à jour du document
        $document->update([
            'is_paid' => true,
            'payment_id' => $payment->id
        ]);

        // Redirection vers la confirmation
        return redirect()->route('citoyen.paiement.confirmation', $payment);
    }

    /**
     * Affiche la confirmation de paiement
     */
    public function showConfirmation(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('citoyen.paiements.confirmation', [
            'payment' => $payment,
            'document' => $payment->document
        ]);
    }

    /**
     * Calcule le montant en fonction du type de document
     */
    private function calculateAmount(string $type): float
    {
        $prices = [
            'naissance' => 2500,
            'mariage' => 3000,
            'deces' => 2500,
            'certificat-vie' => 2000,
            'certificat-revenu' => 2000
        ];

        return $prices[$type] ?? 2500;
    }
}

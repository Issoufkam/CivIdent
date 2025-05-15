<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Flutterwave\Flutterwave;

class PaymentController extends Controller {
    public function initiate(Document $document) {
        $this->authorize('pay', $document);

        $flutterwave = new Flutterwave(config('services.flutterwave.secret_key'));
        $response = $flutterwave->payment->initiate([
            'tx_ref' => 'DOC-' . $document->id,
            'montant' => 2000, // XOF
            'currency' => 'XOF',
            'customer' => [
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
            ],
            'redirect_url' => route('requests.show', $document),
        ]);

        return redirect($response['data']['link']);
    }

    public function handleWebhook(Request $request) {
        // Vérification de la signature Flutterwave
        if (Flutterwave::verifyWebhook($request->all())) {
            $documentId = explode('-', $request->tx_ref)[1];
            $document = Document::find($documentId);
            $document->payment()->update(['status' => 'completed']);
        }
        return response()->json(['status' => 'success']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Acte;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function dashboard()
    {
        return view('agent.dashboard');
    }

    public function index()
    {
        $agent = Auth::user()->agent;
        $demandes = Demande::whereHas('acte', function ($query) use ($agent) {
            $query->where('commune_id', $agent->commune_id);
        })->get();

        return view('agent.demandes.index', compact('demandes'));
    }

    public function show($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('view', $demande);

        return view('agent.demandes.show', compact('demande'));
    }

    public function update(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('update', $demande);

        $request->validate([
            'statut' => 'required|in:en_attente,validée,rejetée,traitée',
        ]);

        $demande->update([
            'statut' => $request->statut,
            'agent_id' => Auth::user()->agent->id,
        ]);

        return redirect()->route('agent.demandes.index')->with('success', 'Demande mise à jour avec succès.');
    }

    public function create()
    {
        return view('agent.actes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_acte' => 'required|unique:actes,numero_acte',
            'date_etablissement' => 'required|date',
            'citoyen_id' => 'required|exists:citoyens,id',
            'type_acte_id' => 'required|exists:type_actes,id',
            'fichier_pdf' => 'nullable|file|mimes:pdf',
        ]);

        $agent = Auth::user()->agent;

        $acte = Acte::create([
            'numero_acte' => $request->numero_acte,
            'date_etablissement' => $request->date_etablissement,
            'citoyen_id' => $request->citoyen_id,
            'type_acte_id' => $request->type_acte_id,
            'commune_id' => $agent->commune_id,
            'fichier_pdf' => $request->file('fichier_pdf') ? $request->file('fichier_pdf')->store('actes') : null,
        ]);

        return redirect()->route('agent.actes.index')->with('success', 'Acte créé avec succès.');
    }

    public function actesIndex()
    {
        $agent = Auth::user()->agent;
        $actes = Acte::where('commune_id', $agent->commune_id)->get();

        return view('agent.actes.index', compact('actes'));
    }
}

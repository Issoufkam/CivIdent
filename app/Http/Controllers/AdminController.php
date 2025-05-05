<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Commune;
use App\Models\Role;
use App\Models\Utilisateur;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /*** AGENTS ***/
    public function index()
    {
        $agents = Agent::with('utilisateur', 'commune')->get();
        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        $communes = Commune::all();
        return view('admin.agents.create', compact('communes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'commune_id' => 'required|exists:communes,id',
        ]);

        $utilisateur = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => Role::where('nom', 'agent')->first()->id,
        ]);

        Agent::create([
            'utilisateur_id' => $utilisateur->id,
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent créé avec succès.');
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $communes = Commune::all();
        return view('admin.agents.edit', compact('agent', 'communes'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $utilisateur = $agent->utilisateur;

        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
            'commune_id' => 'required|exists:communes,id',
        ]);

        $utilisateur->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
        ]);

        $agent->update([
            'commune_id' => $request->commune_id,
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->utilisateur->delete();
        $agent->delete();

        return redirect()->route('admin.agents.index')->with('success', 'Agent supprimé avec succès.');
    }

    /*** ROLES ***/
    public function indexRoles()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function createRole()
    {
        return view('admin.roles.create');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|unique:roles,nom',
            'description' => 'nullable|string',
        ]);

        Role::create($request->only('nom', 'description'));

        return redirect()->route('admin.roles.index')->with('success', 'Rôle créé avec succès.');
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|unique:roles,nom,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->only('nom', 'description'));

        return redirect()->route('admin.roles.index')->with('success', 'Rôle mis à jour.');
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle supprimé.');
    }
}

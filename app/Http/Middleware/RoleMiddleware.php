<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Vérifie si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect('/login');  // Redirige vers la page de login si non authentifié
        }

        $user = Auth::user();
        
        // Vérifie si l'utilisateur a un rôle
        if (!$user->role) {
            abort(403, 'Accès non autorisé - Aucun rôle attribué');  // Si l'utilisateur n'a pas de rôle, accès interdit
        }

        // Vérifie si l'utilisateur possède un des rôles autorisés
        if (!empty($roles) && !in_array($user->role->nom, $roles)) {
            abort(403, 'Accès non autorisé - Rôle insuffisant');  // Si le rôle de l'utilisateur ne correspond à aucun des rôles autorisés
        }

        // Si l'utilisateur est un citoyen et qu'il essaie d'accéder à son dashboard
        if ($user->role->nom == 'citoyen' && $request->is('citoyen/dashboard*')) {
            return $next($request);  // Permet d'accéder au dashboard du citoyen
        }

        // Si l'utilisateur n'est pas un citoyen ou tente d'accéder à une autre page
        if ($user->role->nom != 'citoyen') {
            return redirect()->route('accueil');  // Redirige vers la page d'accueil
        }

        // Si tout est en règle, la requête continue normalement
        return $next($request);
    }
}

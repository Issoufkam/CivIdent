<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CitizenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Ajoute ici ta logique pour vérifier si l'utilisateur est un citoyen
        if (auth()->user() && auth()->user()->role !== 'citizen') {
            return redirect('home'); // Ou n'importe quelle autre redirection
        }

        return $next($request);
    }
}

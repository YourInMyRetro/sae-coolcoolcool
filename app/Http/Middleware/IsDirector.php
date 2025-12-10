<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsDirector{
    public function handle(Request $request, Closure $next):Response{
        if(!Auth::check() || !Auth::user()->isDirector()){
            abort(403,"Accès uniquement réservé au déliceux directeur");
        }
        return $next($request);
    }

}
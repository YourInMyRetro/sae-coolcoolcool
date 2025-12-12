public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->isExpedition()) {
        return redirect('/'); // Rejeté si pas expédition
    }
    return $next($request);
}
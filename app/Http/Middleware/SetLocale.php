<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = 'en';
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        } elseif ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } else {
            $browserLang = substr($request->getPreferredLanguage(['en','de']), 0, 2);
            $locale = in_array($browserLang, ['en','de']) ? $browserLang : 'en';
        }
        App::setLocale($locale);
        return $next($request);
    }
}

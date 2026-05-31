<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(string $lang)
    {
        $lang = in_array($lang, ['en','de']) ? $lang : 'en';
        session(['locale' => $lang]);
        return back();
    }

    public function save(Request $request)
    {
        $lang = in_array($request->locale, ['en','de']) ? $request->locale : 'en';
        session(['locale' => $lang]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $lang]);
        }
        return back();
    }
}

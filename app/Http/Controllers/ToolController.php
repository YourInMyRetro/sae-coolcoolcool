<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ToolController extends Controller
{
    public function index()
    {

        $sourcePath = public_path('temp_images');
        if (!File::isDirectory($sourcePath)) {
            File::makeDirectory($sourcePath, 0755, true);
        }


        $files = File::files($sourcePath);
        
        if (empty($files)) {
            return "<h1>Terminé ! Stock épuisé.</h1>";
        }

        $currentFile = $files[0]->getFilename();

        $produits = DB::table('produit')->orderBy('nom_produit')->get();

        return view('matcher', compact('currentFile', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate(['old_filename' => 'required', 'product_id' => 'required']);

        $produit = DB::table('produit')->where('id_produit', $request->product_id)->first();
        $cleanName = Str::slug($produit->nom_produit) . '.jpg';

        $source = public_path('temp_images/' . $request->old_filename);
        $destinationFolder = public_path('img/produits');
        $destination = $destinationFolder . '/' . $cleanName;

        if (!File::isDirectory($destinationFolder)) {
            File::makeDirectory($destinationFolder, 0755, true);
        }

        if (File::exists($source)) {
            if (File::exists($destination)) { File::delete($destination); }
            File::move($source, $destination);


            




            $exists = DB::table('photo_produit')->where('id_produit', $produit->id_produit)->exists();
            if ($exists) {
                DB::table('photo_produit')->where('id_produit', $produit->id_produit)->update(['url_photo' => 'img/produits/' . $cleanName]);
            } else {
                DB::table('photo_produit')->insert(['url_photo' => 'img/produits/' . $cleanName, 'id_produit' => $produit->id_produit]);
            }
            
            return redirect()->route('matcher.index')->with('success', 'Validé : ' . $cleanName);
        }
        return back()->with('error', 'Fichier introuvable');
    }



    public function skip(Request $request)
    {
        $source = public_path('temp_images/' . $request->old_filename);
        $skippedFolder = public_path('temp_images/_skipped');

        if (!File::isDirectory($skippedFolder)) {
            File::makeDirectory($skippedFolder, 0755, true);
        }

        if (File::exists($source)) {
            File::move($source, $skippedFolder . '/' . $request->old_filename);
            return redirect()->route('matcher.index')->with('warning', 'Image passée (mise de côté).');
        }
        return back();
    }

    public function delete(Request $request)
    {
        $source = public_path('temp_images/' . $request->old_filename);
        
        if (File::exists($source)) {
            File::delete($source);
            return redirect()->route('matcher.index')->with('error', 'Image supprimée définitivement.');
        }
        return back();
    }
}
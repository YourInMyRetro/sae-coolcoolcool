<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Publication::has('blog')
            ->with('blog')
            ->orderBy('date_publication', 'desc')
            ->get();

        return view('blog.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Publication::has('blog')
            ->with(['blog', 'commentaires' => function($q) {
                $q->whereNull('com_id_commentaire')->orderBy('date_depot', 'desc')->with('user', 'reponses.user');
            }])
            ->findOrFail($id);

        return view('blog.show', compact('post'));
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'texte_commentaire' => 'required|string|max:1000',
            'com_id_commentaire' => 'nullable|exists:commentaire,id_commentaire'
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour commenter.');
        }

        $commentaire = new Commentaire();
        $commentaire->id_publication = $id;
        $commentaire->id_utilisateur = Auth::id();
        $commentaire->texte_commentaire = $request->texte_commentaire;
        $commentaire->date_depot = now();
        
        if ($request->filled('com_id_commentaire')) {
            $commentaire->com_id_commentaire = $request->com_id_commentaire;
        }

        $commentaire->save();

        return back()->with('success', 'Votre commentaire a été publié !');
    }
}
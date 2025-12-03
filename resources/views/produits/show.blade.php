@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0;">
    <div class="product-detail" style="display: flex; gap: 40px;">
        
        {{-- Partie Image --}}
        <div class="product-image" style="flex: 1;">
            <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                 alt="{{ $produit->nom_produit }}" 
                 style="width: 100%; max-width: 500px; border-radius: 10px;">
        </div>

        {{-- Partie Infos & Panier --}}
        <div class="product-info" style="flex: 1;">
            {{-- ID 9 : Afficher le nom et la description --}}
            <h1>{{ $produit->nom_produit }}</h1>
            <h3 style="color: #e74c3c;">{{ number_format($produit->premierPrix->prix_total ?? 0, 2) }} €</h3>
            
            @if($tailleSelectionnee)
            <div style="background-color: #e1f5fe; color: #0277bd; padding: 10px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #0288d1;">
                <i class="fas fa-info-circle"></i> 
                Rappel : Vous avez filtré pour la taille <strong>{{ strtoupper($tailleSelectionnee) }}</strong>.
            </div>
            @endif


            <p class="description" style="margin: 20px 0; color: #666;">
                {{ $produit->description_produit }}
            </p>

            {{-- ID 10 : Formulaire pour choisir la couleur et ajouter au panier --}}
            <form action="{{ route('panier.ajouter', $produit->id_produit) }}" method="POST">
                @csrf {{-- Protection obligatoire pour les formulaires Laravel --}}
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="id_couleur"><strong>Choisir une couleur :</strong></label>
                    <select name="id_couleur" id="id_couleur" class="form-control" style="padding: 10px; width: 100%;">
                        @foreach($produit->variantes as $variante)
                            {{-- On affiche le nom de la couleur (Rouge, Bleu...) --}}
                            <option value="{{ $variante->couleur->id_couleur }}">
                                {{ ucfirst($variante->couleur->type_couleur) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-primary" style="padding: 15px 30px; border:none; background: #333; color: white; cursor: pointer;">
                    Ajouter au panier
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
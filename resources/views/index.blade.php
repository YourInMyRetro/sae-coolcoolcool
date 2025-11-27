@extends('layout')

@section('content')
<div class="container" style="padding-top: 40px;">
    
    <h2 class="section-title">Boutique Officielle</h2>

    <form action="{{ route('produits.index') }}" method="GET" class="mb-5" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        
        <div class="search-container" style="margin: 0; max-width: 100%; box-shadow: none; padding: 0; margin-bottom: 20px;">
            <input type="text" name="search" class="search-input" placeholder="Rechercher un maillot, une équipe..." value="{{ request('search') }}">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <label for="sort" style="font-weight: 600; font-size: 0.9rem;">Trier par :</label>
                <select name="sort" id="sort" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">Pertinence</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                </select>
            </div>
            
            <div style="align-self: flex-end;">
                <a href="{{ route('produits.index') }}" style="color: #666; text-decoration: underline; font-size: 0.9rem;">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="grid-produits">
        @forelse($produits as $produit)
            <div class="card-produit">
                <div class="img-container">
                    <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" alt="{{ $produit->nom_produit }}">
                </div>
                <div class="card-info">
                    <span class="prod-cat">Officiel</span>
                    <h3 class="prod-title">{{ $produit->nom_produit }}</h3>
                    <div class="prod-price">
                        {{ $produit->premierPrix->prix_total ?? '--' }} €
                    </div>
                    <a href="{{ route('panier.ajouter', $produit->id_produit) }}" class="prod-btn">Ajouter au panier</a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <h3>Aucun produit trouvé</h3>
            </div>
        @endforelse
    </div>
</div>
@endsection
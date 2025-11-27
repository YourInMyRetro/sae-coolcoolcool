@extends('layout')

@section('content')
    <div class="container" style="padding-top: 40px;">
        
        <form action="{{ route('produits.index') }}" method="GET" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Rechercher un maillot, une équipe, un accessoire..." value="{{ request('search') }}">
            <button type="submit" class="search-btn">RECHERCHER</button>
        </form>

        <h2 style="margin-bottom: 20px;">Tous les Produits</h2>

        <div class="grid">
            @forelse($produits as $produit)
                <div class="card">
                    <div class="card-img">
                        <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" alt="{{ $produit->nom_produit }}">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $produit->nom_produit }}</h3>
                        <p style="font-size: 0.9rem; color: #666; height: 40px; overflow: hidden;">
                            {{ Str::limit($produit->description_produit, 60) }}
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                            <div class="card-price">{{ $produit->premierPrix->prix_total ?? '--' }} €</div>
                            <a href="#" style="color: var(--fifa-blue); font-weight: bold; font-size: 0.9rem;">Voir détails</a>
                        </div>
                        <button class="card-btn">Ajouter au panier</button>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1/-1; text-align: center; font-size: 1.2rem;">
                    Aucun produit ne correspond à votre recherche.
                </p>
            @endforelse
        </div>
    </div>
@endsection
@extends('layout')

@section('content')
    <div class="hero-section">
        <h1 class="hero-title">Vivez votre<br>Passion</h1>
        <p class="hero-subtitle">Le maillot officiel de votre équipe vous attend.</p>
        
        <a href="{{ route('produits.index') }}" class="cta-big">
            Accéder à la Boutique Officielle
        </a>
    </div>

    <div class="container">
        <h2 class="section-title">Nouveautés Populaires</h2>
        <div class="grid-produits">
            @foreach($featuredProducts as $produit)
                <div class="card-produit">
                    <div class="img-container">
                        <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" alt="{{ $produit->nom_produit }}">
                    </div>
                    <div class="card-info">
                        <span class="prod-cat">{{ $produit->categorie->nom_categorie ?? 'Officiel' }}</span>
                        <h3 class="prod-title">{{ $produit->nom_produit }}</h3>
                        <div class="prod-price">{{ $produit->premierPrix->prix_total ?? '--' }} €</div>
                        <a href="#" class="prod-btn">Voir le produit</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
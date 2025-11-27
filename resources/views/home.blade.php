@extends('layout')

@section('content')

{{-- SECTION HERO : Bannière Principale avec Image de fond officielle --}}
<section class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h2 class="hero-subtitle">LA COLLECTION OFFICIELLE</h2>
        <h1 class="hero-title">COUPE DU MONDE 2026™</h1>
        <p class="hero-text">Préparez-vous pour le plus grand événement sportif. Maillots, accessoires et la passion du jeu.</p>
        <div class="hero-buttons">
            <a href="{{ route('produits.index') }}" class="btn btn-fifa-primary">Acheter maintenant</a>
            <a href="{{ route('produits.index', ['categorie' => 1]) }}" class="btn btn-fifa-outline">Voir les Maillots</a>
        </div>
    </div>
</section>

{{-- SECTION CATÉGORIES RAPIDES --}}
<div class="container quick-categories">
    <div class="section-header-center">
        <h3 class="section-heading">PARCOURIR PAR CATÉGORIE</h3>
    </div>
    <div class="categories-grid">
        {{-- Carte Nation --}}
        <a href="{{ route('produits.index', ['nation' => '1']) }}" class="category-card">
            <div class="cat-image" style="background-image: url('https://digitalhub.fifa.com/transform/074e5083-f77c-4742-8703-b0972cb74026/FWC26-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="cat-overlay"></div>
            <span class="cat-title">PAR NATION</span>
        </a>
        
        {{-- Carte Maillots --}}
        <a href="{{ route('produits.index', ['categorie' => '1']) }}" class="category-card">
            <div class="cat-image" style="background-image: url('https://digitalhub.fifa.com/transform/85790483-34b7-4486-9304-7a3536869d5d/Fifa-Store-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="cat-overlay"></div>
            <span class="cat-title">MAILLOTS</span>
        </a>
        
        {{-- Carte Accessoires (adapte l'ID de catégorie si besoin) --}}
        <a href="{{ route('produits.index', ['categorie' => '3']) }}" class="category-card">
            <div class="cat-image" style="background-image: url('https://digitalhub.fifa.com/transform/c2b83446-559d-407a-9694-554446c65c26/FWC22-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="cat-overlay"></div>
            <span class="cat-title">ACCESSOIRES</span>
        </a>
    </div>
</div>

{{-- SECTION TENDANCES (Produits Dynamiques) --}}
<div class="container featured-section">
    <div class="section-header">
        <h3 class="section-heading-left">TENDANCES DU MOMENT</h3>
        <a href="{{ route('produits.index') }}" class="see-all-link">Tout voir <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="products-grid-home">
        @foreach($featuredProducts as $produit)
        <div class="product-card">
            <a href="{{ route('produits.show', $produit->id_produit) }}" class="product-link">
                <div class="product-image-wrapper">
                    @if($produit->premierePhoto)
                        <img src="{{ $produit->premierePhoto->url_photo }}" alt="{{ $produit->nom_produit }}">
                    @else
                        <div class="no-image">Pas d'image</div>
                    @endif
                    
                    {{-- Badge Nouveau sur le premier élément --}}
                    @if($loop->first) <span class="badge-new">NOUVEAU</span> @endif
                </div>
                <div class="product-details">
                    <span class="product-category">
                        {{-- Gestion sécurisée si la catégorie est nulle --}}
                        {{ $produit->categorie->nom_categorie ?? 'Officiel' }}
                    </span>
                    <h4 class="product-name">{{ $produit->nom_produit }}</h4>
                    <div class="product-price">
                        {{ $produit->premierPrix ? number_format($produit->premierPrix->prix_total, 2) . ' €' : 'Prix indisponible' }}
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- SECTION PROMO SECONDAIRE --}}
<section class="promo-banner">
    <div class="promo-content">
        <h2>REJOIGNEZ LE CLUB</h2>
        <p>Inscrivez-vous pour recevoir les dernières nouvelles et offres exclusives sur la Coupe du Monde.</p>
        <a href="#" class="btn btn-fifa-white">S'inscrire gratuitement</a>
    </div>
</section>

@endsection
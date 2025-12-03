@extends('layout')

@section('content')
<div class="container page-boutique">
    
    <div class="shop-header">
        <h2 class="section-title">
            @if(request('search'))
                Résultats pour "{{ request('search') }}"
            @else
                Boutique Officielle
            @endif
        </h2>
        <span class="results-count">{{ count($produits) }} article(s) trouvé(s)</span>
    </div>

    <form action="{{ route('produits.index') }}" method="GET" class="filters-bar">
        
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <div class="filter-group">
            <label><i class="fas fa-sort"></i> Trier</label>
            <select name="sort" onchange="this.form.submit()">
                <option value="">Pertinence</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
            </select>
        </div>

        <div class="filter-group">
            <label><i class="fas fa-palette"></i> Couleur</label>
            <select name="couleur" onchange="this.form.submit()">
                <option value="">Toutes</option>
                @foreach($allColors as $color)
                    <option value="{{ $color }}" {{ request('couleur') == $color ? 'selected' : '' }}>{{ ucfirst($color) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label><i class="fas fa-ruler"></i> Taille</label>
            <select name="taille" onchange="this.form.submit()">
                <option value="">Toutes</option>
                @foreach($allSizes as $size)
                    <option value="{{ $size }}" {{ request('taille') == $size ? 'selected' : '' }}>{{ strtoupper($size) }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-reset">
            <a href="{{ route('produits.index') }}">Réinitialiser tout</a>
        </div>
    </form>

    @if(count($produits) > 0)
        <div class="products-grid">
            @foreach($produits as $produit)
                <div class="product-card">
                <div class="card-img">
                    {{-- On passe un tableau : l'ID + la taille récupérée de la requête actuelle --}}
                    <a href="{{ route('produits.show', ['id' => $produit->id_produit, 'taille' => request('taille')]) }}">
                        <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" alt="{{ $produit->nom_produit }}">
                    </a>
                </div>
                    <div class="card-body">
                        <span class="card-category">
                            @foreach($produit->nations as $nation) {{ $nation->nom_nation }} @endforeach
                            {{ $produit->categorie->nom_categorie ?? '' }}
                        </span>
                        <h3 class="card-title">
                            <a href="{{ route('produits.show', ['id' => $produit->id_produit, 'taille' => request('taille')]) }}" style="color: inherit; text-decoration: none;">
                                {{ $produit->nom_produit }}
                            </a>
                        </h3>
                        <div class="card-price">
                            {{ number_format($produit->premierPrix->prix_total ?? 0, 2) }} €
                        </div>
                        <a href="{{ route('panier.ajouter', $produit->id_produit) }}" class="btn-add">
                            Ajouter au panier
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-results">
            <i class="fas fa-search" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3>Oups ! Aucun produit ne correspond.</h3>
            <p>Essayez de chercher "Argentine", "Maillot" ou "Ballon".</p>
            <a href="{{ route('produits.index') }}" class="btn-primary" style="margin-top: 20px;">Voir tout le catalogue</a>
        </div>
    @endif
</div>
@endsection
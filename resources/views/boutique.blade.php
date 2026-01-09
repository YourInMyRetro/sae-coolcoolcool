@extends('layout')

@section('content')
<div class="container shop-page-container" style="padding: 40px 20px; max-width: 1400px;">

    <div style="margin-bottom: 30px; text-align: center;">
        <h1 style="color: #003366; font-size: 2.5rem; margin-bottom: 10px;">
            La Boutique Officielle
            <i class="fas fa-store help-trigger" 
               style="font-size: 0.5em; vertical-align: middle; color: #326295;"
               data-title="Bienvenue !" 
               data-content="Vous êtes dans la boutique. Utilisez les menus à gauche pour trier les produits par équipe ou par type (maillot, ballon...)."
               data-link="{{ route('aide') }}#section-boutique"></i>
        </h1>
        <p style="color: #666;">Retrouvez tous les équipements officiels de vos équipes préférées.</p>
    </div>

    <form action="{{ route('produits.index') }}" method="GET" id="filterForm" class="shop-layout" style="display: flex; gap: 40px; align-items: flex-start;">
        
        <aside class="shop-sidebar" style="width: 300px; flex-shrink: 0; background: white; padding: 25px; border-radius: 8px; border: 1px solid #e1e8ed; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            
            <div class="sidebar-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #326295;">
                <h3 style="margin: 0; color: #326295;">
                    <i class="fas fa-filter"></i> Filtres 
                    <i class="fas fa-question-circle help-trigger" 
                       data-title="Comment filtrer ?" 
                       data-content="Cochez les cases pour affiner votre recherche. Par exemple : sélectionnez 'France' pour ne voir que les produits français."></i>
                </h3>
                <a href="{{ route('produits.index') }}" class="reset-filters" style="font-size: 0.85rem; color: #e74c3c; text-decoration: underline;">Tout effacer</a>
            </div>

            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="filter-group" style="margin-bottom: 30px;">
                <h4 class="filter-title" style="font-size: 1.1rem; color: #333; margin-bottom: 15px; font-weight: bold;">
                    Catégories 
                    <i class="fas fa-tshirt help-trigger" 
                       style="float: right; color: #ccc;"
                       data-title="Types de produits" 
                       data-content="Cherchez-vous un maillot, un short ou un accessoire ?"></i>
                </h4>
                
                <div class="filter-options">
                    <label class="custom-radio parent-cat" style="margin-bottom: 10px; font-weight: bold; display: block; cursor: pointer;">
                        <input type="radio" name="categorie" value="" onchange="this.form.submit()" {{ request('categorie') == '' ? 'checked' : '' }} style="margin-right: 8px;">
                        <span class="cat-name">TOUT VOIR</span>
                    </label>

                    @foreach($categoryGroups as $groupName => $categories)
                        <details class="category-accordion" open style="margin-bottom: 10px;">
                            <summary class="parent-label" style="font-weight: 600; color: #555; cursor: pointer; padding: 5px 0;">
                                {{ $groupName }}
                            </summary>
                            
                            <div class="subcategory-list" style="padding-left: 15px; margin-top: 5px;">
                                @foreach($categories as $cat)
                                <label class="custom-radio" style="display: block; margin-bottom: 5px; cursor: pointer; color: #666;">
                                    <input type="radio" name="categorie" value="{{ $cat->id_categorie }}" onchange="this.form.submit()" {{ request('categorie') == $cat->id_categorie ? 'checked' : '' }} style="margin-right: 8px;">
                                    <span>{{ $cat->nom_categorie }}</span>
                                </label>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>

            <div class="filter-group" style="margin-bottom: 30px;">
                <h4 class="filter-title" style="font-size: 1.1rem; color: #333; margin-bottom: 15px; font-weight: bold;">
                    Équipes 
                    <i class="fas fa-flag help-trigger" 
                       style="float: right; color: #ccc;"
                       data-title="Supporter" 
                       data-content="Sélectionnez un pays pour voir uniquement les produits de cette équipe."></i>
                </h4>
                <div class="filter-options" style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                    <label class="custom-radio" style="display: block; margin-bottom: 8px; cursor: pointer;">
                        <input type="radio" name="nation" value="" onchange="this.form.submit()" {{ request('nation') == '' ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Toutes les équipes</span>
                    </label>
                    @foreach($allNations as $nat)
                    <label class="custom-radio" style="display: block; margin-bottom: 8px; cursor: pointer;">
                        <input type="radio" name="nation" value="{{ $nat->id_nation }}" onchange="this.form.submit()" {{ request('nation') == $nat->id_nation ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>{{ $nat->nom_nation }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-group" style="margin-bottom: 30px;">
                <h4 class="filter-title" style="font-size: 1.1rem; color: #333; margin-bottom: 15px; font-weight: bold;">
                    Couleurs
                    <i class="fas fa-palette help-trigger" 
                       style="float: right; color: #ccc;"
                       data-title="Choix couleur" 
                       data-content="Si vous voulez absolument un maillot bleu, cliquez sur la pastille bleue."></i>
                </h4>
                
                <label class="custom-radio" style="margin-bottom: 10px; display: block; cursor: pointer;">
                    <input type="radio" name="couleur" value="" onchange="this.form.submit()" {{ request('couleur') == '' ? 'checked' : '' }} style="margin-right: 8px;">
                    <span>Toutes</span>
                </label>

                <div class="color-grid" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($allColors as $c)
                    <label class="color-swatch" title="{{ ucfirst($c) }}" style="cursor: pointer; position: relative;">
                        <input type="radio" name="couleur" value="{{ $c }}" onchange="this.form.submit()" {{ request('couleur') == $c ? 'checked' : '' }} style="display: none;">
                        
                        <span style="
                            display: block; 
                            width: 25px; 
                            height: 25px; 
                            border-radius: 50%; 
                            background: {{ $c == 'Or' ? 'gold' : ($c == 'Multicolore' ? 'linear-gradient(135deg, red, blue, yellow)' : ($c == 'Argent' ? 'silver' : $c)) }};
                            border: 1px solid #ccc;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                            transition: transform 0.2s, box-shadow 0.2s;
                            {{ request('couleur') == $c ? 'transform: scale(1.2); border: 2px solid #333;' : '' }}
                        "></span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-group">
                <h4 class="filter-title" style="font-size: 1.1rem; color: #333; margin-bottom: 15px; font-weight: bold;">
                    Tailles
                    <i class="fas fa-ruler-combined help-trigger" 
                       style="float: right; color: #ccc;"
                       data-title="Votre taille" 
                       data-content="Ne perdez pas de temps à regarder des produits qui ne vous iront pas. Filtrez directement par votre taille !"></i>
                </h4>
                <div class="size-grid" style="display: flex; flex-wrap: wrap; gap: 5px;">
                    <label class="size-box" style="cursor: pointer;">
                        <input type="radio" name="taille" value="" onchange="this.form.submit()" {{ request('taille') == '' ? 'checked' : '' }} style="display: none;">
                        <span style="display: block; padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; {{ request('taille') == '' ? 'background: #333; color: white;' : 'background: white;' }}">Tout</span>
                    </label>
                    @foreach($allSizes as $t)
                    <label class="size-box" style="cursor: pointer;">
                        <input type="radio" name="taille" value="{{ $t }}" onchange="this.form.submit()" {{ request('taille') == $t ? 'checked' : '' }} style="display: none;">
                        <span style="display: block; padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; {{ request('taille') == $t ? 'background: #326295; color: white;' : 'background: white;' }}">
                            {{ strtoupper($t) }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
        </aside>

        <main class="shop-results" style="flex-grow: 1;">
            
            <div class="results-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 15px; border-radius: 8px; border: 1px solid #e1e8ed;">
                <div class="results-count" style="color: #666;">
                    @if(request('search'))
                        Résultats pour "<strong>{{ request('search') }}</strong>" ({{ $produits->count() }})
                    @else
                        <strong>{{ $produits->count() }}</strong> produits trouvés
                    @endif
                </div>
                
                <div class="sort-wrapper" style="display: flex; align-items: center; gap: 10px;">
                    <label for="sort" style="font-weight: bold; color: #333;">Trier par :</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" class="sort-select" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Pertinence</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant (Moins cher)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant (Plus cher)</option>
                    </select>
                    <i class="fas fa-sort help-trigger" 
                       style="color: #326295;"
                       data-title="Ordre d'affichage" 
                       data-content="Vous avez un petit budget ? Choisissez 'Prix croissant' pour voir les bonnes affaires en premier."></i>
                </div>
            </div>

            @if($produits->count() > 0)
                <div class="products-grid-shop" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px;">
                    @foreach($produits as $produit)

                    <div class="product-card shop-card" style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden; transition: transform 0.2s; display: flex; flex-direction: column; height: 100%;">
                        
                        <a href="{{ route('produits.show', ['id' => $produit->id_produit, 'taille' => request('taille')]) }}" class="product-link" style="flex-grow: 1; text-decoration: none; color: inherit; display: block;">
                            <div class="product-image-wrapper" style="height: 220px; overflow: hidden; position: relative;">
                                @if($produit->premierePhoto)
                                    <img src="{{ $produit->premierePhoto->url_photo }}" alt="{{ $produit->nom_produit }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                                @else
                                    <div class="no-image" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #eee; color: #999;">Pas d'image</div>
                                @endif
                                
                                <span style="position: absolute; top: 10px; left: 10px; background: #00cfb7; color: #003366; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                                    Officiel
                                </span>
                            </div>
                            
                            <div class="product-details" style="padding: 15px;">
                                <span class="product-category-small" style="display: block; color: #999; font-size: 0.8rem; margin-bottom: 5px; text-transform: uppercase;">
                                    {{ $produit->nations->first()->nom_nation ?? ($produit->categorie->nom_categorie ?? 'FIFA') }}
                                </span>
                                <h4 class="product-name" style="margin: 0 0 10px 0; font-size: 1rem; color: #333; line-height: 1.4; height: 2.8em; overflow: hidden;">
                                    {{ $produit->nom_produit }}
                                </h4>
                                <div class="product-price" style="font-weight: bold; color: #326295; font-size: 1.2rem;">
                                    {{ $produit->premierPrix ? number_format($produit->premierPrix->prix_total, 2) . ' €' : 'N/A' }}
                                </div>
                            </div>
                        </a>

                        <div style="padding: 0 15px 15px 15px; margin-top: auto; display:flex; gap: 10px; align-items: center;">
                            <a href="{{ route('produits.show', ['id' => $produit->id_produit]) }}" 
                               class="btn-view-product"
                               style="flex-grow:1; display: block; padding: 10px 0; background-color: #326295; color: white; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 0.8rem; border-radius: 4px; text-decoration: none; transition: background 0.2s;">
                                Voir le produit
                            </a>
                            
                            <div style="cursor: pointer;">
                                <i class="fas fa-search-plus help-trigger" 
                                   style="font-size: 1.2em; color: #888;"
                                   data-title="Aperçu" 
                                   data-content="Cliquez sur 'Voir le produit' pour choisir votre taille et l'ajouter au panier."></i>
                            </div>
                        </div>

                    </div>
                    
                    @endforeach
                </div>
            @else
                <div class="no-results" style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                    <i class="fas fa-search" style="font-size: 3rem; color: #eee; margin-bottom: 20px;"></i>
                    <p style="font-size: 1.2rem; color: #666;">Aucun produit ne correspond à vos critères.</p>
                    <a href="{{ route('produits.index') }}" style="color: #326295; text-decoration: underline;">Réinitialiser les filtres</a>
                </div>
            @endif
        </main>
    </form>
</div>
@endsection
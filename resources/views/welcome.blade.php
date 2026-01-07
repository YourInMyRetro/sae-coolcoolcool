@extends('layout')

@section('title', 'Accueil - Inside FIFA')

@section('content')

{{-- SECTION 1 : HERO BANNER (Grande Image d'accueil) --}}
{{-- J'utilise l'image du ballon de la finale, c'est souvent très qualitatif pour une bannière --}}
<div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light hero-banner" 
     style="background-image: url('{{ asset('img/produits/ballon-ucl-2025-finale.jpg') }}'); background-size: cover; background-position: center;">
    
    <div class="col-md-5 p-lg-5 mx-auto my-5 bg-dark p-4 rounded bg-opacity-75 text-white">
        <h1 class="display-4 fw-normal">Inside FIFA</h1>
        <p class="lead fw-normal">Plongez au cœur du football mondial. Boutique officielle, votes exclusifs et actualités en temps réel.</p>
        <a class="btn btn-outline-light btn-lg" href="{{ route('produits.index') }}">
            <i class="fas fa-shopping-bag me-2"></i>Accéder à la Boutique
        </a>
    </div>
    <div class="product-device shadow-sm d-none d-md-block"></div>
    <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
</div>

{{-- SECTION 2 : LES ACCÈS RAPIDES (3 Cartes) --}}
<div class="container my-5">
    <div class="row g-4">
        
        {{-- CARTE 1 : BOUTIQUE --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-zoom">
                {{-- Image : Le nouveau maillot de la France --}}
                <div class="overflow-hidden" style="height: 250px;">
                    <img src="{{ asset('img/produits/maillot-france-2026-authentic.jpg') }}" 
                         class="card-img-top h-100 w-100 object-fit-cover" 
                         alt="Boutique FIFA">
                </div>
                <div class="card-body">
                    <h3 class="h4 card-title"><i class="fas fa-tshirt text-primary me-2"></i>Boutique</h3>
                    <p class="card-text text-muted">Découvrez les maillots officiels 2026, les crampons pro et les accessoires collectors.</p>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="{{ route('produits.index') }}" class="btn btn-primary w-100">
                        Voir les produits <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARTE 2 : ZONE DE VOTE --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-zoom">
                {{-- Image : Kylian Mbappé --}}
                <div class="overflow-hidden" style="height: 250px;">
                    <img src="{{ asset('img/vote/kylian-mbappe.jpg') }}" 
                         class="card-img-top h-100 w-100 object-fit-cover" 
                         alt="Votes Joueurs">
                </div>
                <div class="card-body">
                    <h3 class="h4 card-title"><i class="fas fa-vote-yea text-success me-2"></i>Espace Votes</h3>
                    <p class="card-text text-muted">Élisez le joueur du mois ! Mbappé, Haaland, Vinicius... Votre avis compte pour le trophée The Best.</p>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="{{ route('vote.index') }}" class="btn btn-success w-100">
                        Voter maintenant <i class="fas fa-check-circle ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARTE 3 : ACTUALITÉS / BLOG --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-zoom">
                {{-- Image : La Nissan 370Z (Mascotte du blog) --}}
                <div class="overflow-hidden" style="height: 250px;">
                    <img src="{{ asset('img/produits/1767607760_2017-nissan-370z_100556352.jpg') }}" 
                         class="card-img-top h-100 w-100 object-fit-cover" 
                         alt="Actualités FIFA">
                </div>
                <div class="card-body">
                    <h3 class="h4 card-title"><i class="fas fa-newspaper text-danger me-2"></i>Actualités</h3>
                    <p class="card-text text-muted">Tests produits, interviews exclusives et coulisses. Découvrez notre essai de la Nissan 370Z !</p>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="{{ route('blog.index') }}" class="btn btn-danger w-100">
                        Lire le Blog <i class="fas fa-book-open ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- SECTION 3 : NEWSLETTER / FOOTER CALL --}}
<div class="bg-dark text-white py-5 mt-5">
    <div class="container text-center">
        <h2 class="mb-3">Rejoignez la communauté FIFA</h2>
        <p class="lead mb-4 text-white-50">Inscrivez-vous pour recevoir les offres exclusives et les alertes votes.</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Votre email..." aria-label="Email">
                    <button class="btn btn-primary" type="button">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Petit ajout CSS pour l'effet de zoom au survol des cartes */
    .hover-zoom {
        transition: transform 0.3s ease;
    }
    .hover-zoom:hover {
        transform: translateY(-5px);
    }
    .object-fit-cover {
        object-fit: cover; /* Assure que l'image remplit la case sans être déformée */
    }
    .bg-opacity-75 {
        background-color: rgba(33, 37, 41, 0.75) !important; /* Fond sombre transparent pour le texte Hero */
    }
</style>
@endsection
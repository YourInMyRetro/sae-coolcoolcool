@extends('layout')

@section('title', 'Accueil - Inside FIFA')

@section('content')

{{-- STYLE SPÉCIFIQUE POUR L'ACCUEIL --}}
<style>
    /* Effet Parallaxe et Image de fond sombre */
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('{{ asset("img/produits/ballon-ucl-2025-finale.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        padding: 150px 0;
        text-align: center;
        margin-top: -1.5rem; /* Pour coller au menu si nécessaire */
    }

    /* Animations des cartes */
    .feature-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s ease;
        background: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .feature-card .card-img-wrapper {
        height: 250px;
        overflow: hidden;
    }

    .feature-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .feature-card:hover img {
        transform: scale(1.1); /* Zoom sur l'image au survol */
    }

    .btn-fifa {
        background-color: #326295; /* Bleu FIFA */
        color: white;
        border-radius: 50px;
        padding: 10px 30px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }

    .btn-fifa:hover {
        background-color: #244a75;
        color: white;
        transform: scale(1.05);
    }
    
    .section-title {
        position: relative;
        display: inline-block;
        margin-bottom: 3rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -15px;
        transform: translateX(-50%);
        width: 50px;
        height: 4px;
        background-color: #326295;
    }
</style>

{{-- 1. HERO BANNER --}}
<div class="hero-section mb-5">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">INSIDE FIFA</h1>
        <p class="lead mb-4 fs-4">Vivez le football de l'intérieur. Boutique officielle, votes exclusifs et actualités.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('produits.index') }}" class="btn btn-fifa btn-lg">
                <i class="fas fa-shopping-bag me-2"></i> La Boutique
            </a>
            <a href="{{ route('vote.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                <i class="fas fa-vote-yea me-2"></i> Voter
            </a>
        </div>
    </div>
</div>

{{-- 2. LES 3 PILIERS (BOUTIQUE, VOTES, BLOG) --}}
<div class="container my-5">
    <div class="text-center">
        <h2 class="section-title">Explorez l'Univers FIFA</h2>
    </div>

    <div class="row g-4">
        
        {{-- CARTE 1 : BOUTIQUE --}}
        <div class="col-md-4">
            <div class="feature-card">
                <div class="card-img-wrapper">
                    {{-- Image : Maillot France --}}
                    <img src="{{ asset('img/produits/maillot-france-2026-authentic.jpg') }}" alt="Boutique">
                </div>
                <div class="card-body text-center p-4">
                    <h3 class="h4 fw-bold mb-3">Boutique Officielle</h3>
                    <p class="text-muted mb-4">Retrouvez les maillots officiels 2026, les collections vintages et les équipements pro.</p>
                    <a href="{{ route('produits.index') }}" class="text-primary fw-bold text-decoration-none">
                        Accéder à la boutique <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARTE 2 : VOTES --}}
        <div class="col-md-4">
            <div class="feature-card">
                <div class="card-img-wrapper">
                    {{-- Image : Mbappé --}}
                    <img src="{{ asset('img/vote/kylian-mbappe.jpg') }}" alt="Votes">
                </div>
                <div class="card-body text-center p-4">
                    <h3 class="h4 fw-bold mb-3">Espace Votes</h3>
                    <p class="text-muted mb-4">Faites entendre votre voix ! Élisez le joueur du mois et participez aux trophées The Best.</p>
                    <a href="{{ route('vote.index') }}" class="text-success fw-bold text-decoration-none">
                        Voter maintenant <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARTE 3 : ACTUALITÉS --}}
        <div class="col-md-4">
            <div class="feature-card">
                <div class="card-img-wrapper">
                    {{-- Image : Nissan 370Z --}}
                    <img src="{{ asset('img/produits/1767607760_2017-nissan-370z_100556352.jpg') }}" alt="Actualités">
                </div>
                <div class="card-body text-center p-4">
                    <h3 class="h4 fw-bold mb-3">Actualités & Tests</h3>
                    <p class="text-muted mb-4">Interviews exclusives, tests de matériel et coulisses des plus grands stades.</p>
                    <a href="{{ route('blog.index') }}" class="text-danger fw-bold text-decoration-none">
                        Lire les articles <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. SECTION "À LA UNE" --}}
<div class="bg-light py-5 mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <span class="badge bg-primary mb-2">NOUVEAUTÉ</span>
                <h2 class="fw-bold mb-3">Le Ballon Officiel de la Finale</h2>
                <p class="lead text-muted">Découvrez le "Fussballliebe", bijou de technologie conçu pour l'élite du football mondial. Précision, contrôle et aérodynamisme repensés.</p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Technologie Connected Ball</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Texture micro-rugueuse</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Validé par la FIFA Quality Pro</li>
                </ul>
                <a href="{{ route('produits.index') }}" class="btn btn-dark mt-3">Commander le vôtre</a>
            </div>
            <div class="col-md-6">
                <div class="rounded-3 overflow-hidden shadow-lg">
                    <img src="{{ asset('img/produits/ballon-ucl-2025-finale.jpg') }}" class="img-fluid w-100" alt="Ballon Finale">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. NEWSLETTER / FOOTER CALL --}}
<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <h2 class="mb-3 fw-bold">Rejoignez la communauté FIFA</h2>
        <p class="mb-4 text-white-50">Recevez en avant-première les sorties de maillots et les résultats des votes.</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="#" class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Votre email..." aria-label="Email">
                    <button class="btn btn-primary px-4" type="button">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
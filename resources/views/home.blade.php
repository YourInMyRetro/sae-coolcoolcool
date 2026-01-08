@extends('layout')

@section('content')

{{-- 1. MAIN HERO : L'affiche principale --}}
<section class="fifa-hero-fullscreen">
    <video autoplay muted playsinline loop preload="auto" class="video-bg" poster="{{ asset('img/produits/ballon-ucl-2025-finale.jpg') }}">
        <source src="{{ asset('img/Intro.mp4') }}" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo
    </video>
    <div class="hero-overlay-dark"></div>
    <div class="hero-content-center">
        <h2 class="hero-sub">BOUTIQUE OFFICIELLE</h2>
        <h1 class="hero-main-title">LA PASSION DU FOOTBALL</h1>
        <div class="hero-actions">
            <a href="{{ route('produits.index') }}" class="btn-fifa-cta">Découvrir la collection</a>
        </div>
    </div>
</section>

{{-- 2. NAVIGATION VISUELLE (HOMME / FEMME / ENFANT) --}}
<div class="container section-spacer">
    <div class="visual-nav-grid">
        {{-- Carte Homme --}}
        <a href="{{ route('produits.index', ['categorie' => 2]) }}" class="visual-card">
            {{-- Image locale --}}
            <div class="visual-bg" style="background-image: url('{{ asset('img/produits/maillot-france-2026-authentic.jpg') }}');"></div>
            <div class="visual-content">
                <h3>HOMMES</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>

        {{-- Carte Femme --}}
        <a href="{{ route('produits.index', ['categorie' => 3]) }}" class="visual-card">
            {{-- Image locale --}}
            <div class="visual-bg" style="background-image: url('{{ asset('img/produits/maillotjaponfemme2022.jpg') }}');"></div>
            <div class="visual-content">
                <h3>FEMMES</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>

        {{-- Carte Enfant --}}
        <a href="{{ route('produits.index', ['categorie' => 4]) }}" class="visual-card">
            {{-- Image locale --}}
            <div class="visual-bg" style="background-image: url('{{ asset('img/produits/pelucheMascottecdm.jpg') }}');"></div>
            <div class="visual-content">
                <h3>ENFANTS</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>
    </div>
</div>

{{-- 3. BANNIÈRE ÉVÉNEMENT --}}
<section class="event-banner-wide">
    <div class="event-content">
        <img src="https://digitalhub.fifa.com/m/6956530336a7b484/original/FIFA-Store-Logo-White.png" alt="FIFA" class="event-logo">
        <h2>COUPE DU MONDE 2026™</h2>
        <p>Préparez-vous pour le plus grand événement de l'histoire.</p>
        <a href="{{ route('produits.index', ['nation' => 1]) }}" class="btn-fifa-outline-big">Voir les nations</a>
    </div>
</section>

{{-- 4. CATÉGORIES POPULAIRES --}}
<div class="container section-spacer">
    <h3 class="section-title-center">PARCOURIR PAR TYPE</h3>
    <div class="categories-circles-grid">
        <a href="{{ route('produits.index', ['categorie' => 1]) }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('{{ asset('img/produits/maillot-allemagne-domicile-2024.jpg') }}');"></div>
            <span>MAILLOTS</span>
        </a>
        <a href="{{ route('produits.index', ['categorie' => 1]) }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('{{ asset('img/produits/vestedesurvetementnoireallemagne.jpg') }}');"></div>
            <span>ENTRAINEMENT</span>
        </a>
        <a href="{{ route('produits.index', ['categorie' => 3]) }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('{{ asset('img/produits/echarpe-france-bleu.jpg') }}');"></div>
            <span>ACCESSOIRES</span>
        </a>
        <a href="{{ route('produits.index') }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('{{ asset('img/produits/repliqueTropheecoupedumonde2014.jpg') }}');"></div>
            <span>CADEAUX</span>
        </a>
    </div>
</div>

{{-- ========================================================= --}}
{{-- LE CSS MANQUANT (C'est ça qui répare ton affichage !) --}}
{{-- ========================================================= --}}
<style>
    /* 1. Hero Fullscreen */
    .fifa-hero-fullscreen {
        position: relative;
        height: 85vh; /* Prend 85% de la hauteur de l'écran */
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-top: -24px; /* Ajuste si besoin selon ton menu */
        background-color: #000;
    }

    .video-bg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: 0;
        object-fit: cover;
    }

    .hero-overlay-dark {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.3); /* Assombrit un peu la vidéo */
        z-index: 1;
    }

    .hero-content-center {
        position: relative;
        text-align: center;
        z-index: 2;
    }

    .hero-main-title {
        font-size: 4rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 2rem;
        text-shadow: 0 4px 10px rgba(0,0,0,0.6);
    }

    .hero-sub {
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 4px;
        margin-bottom: 1rem;
        color: #ddd;
    }

    .btn-fifa-cta {
        background-color: #326295;
        color: white;
        padding: 15px 40px;
        font-size: 1.1rem;
        font-weight: bold;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 50px;
        transition: background 0.3s;
        display: inline-block;
    }
    .btn-fifa-cta:hover { background-color: #244a75; color: white; }

    /* 2. Visual Grid */
    .section-spacer { margin: 80px auto; }
    
    .visual-nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .visual-card {
        position: relative;
        height: 400px;
        display: flex;
        align-items: flex-end;
        text-decoration: none;
        color: white;
        overflow: hidden;
        border-radius: 4px;
    }

    .visual-bg {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-size: cover;
        background-position: center;
        transition: transform 0.5s ease;
        z-index: 0;
    }
    
    .visual-card:hover .visual-bg { transform: scale(1.05); }

    .visual-content {
        position: relative;
        padding: 30px;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        width: 100%;
        z-index: 1;
    }

    .visual-content h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .fake-link {
        font-weight: 600;
        text-decoration: underline;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    /* 3. Event Banner */
    .event-banner-wide {
        position: relative;
        background: #111;
        color: white;
        padding: 100px 20px;
        text-align: center;
        /* Image de fond pour la bannière */
        background-image: url('{{ asset("img/produits/ballon-ucl-2025-finale.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; /* Effet parallaxe */
    }
    
    /* Filtre sombre sur la bannière */
    .event-banner-wide::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 0;
    }

    .event-content {
        position: relative;
        z-index: 1;
    }
    
    .event-logo { width: 120px; margin-bottom: 20px; }
    .event-banner-wide h2 { font-size: 2.5rem; font-weight: 900; margin-bottom: 10px; }
    
    .btn-fifa-outline-big {
        display: inline-block;
        margin-top: 20px;
        border: 2px solid white;
        color: white;
        padding: 12px 35px;
        font-weight: bold;
        text-transform: uppercase;
        text-decoration: none;
        transition: 0.3s;
    }
    .btn-fifa-outline-big:hover { background: white; color: black; }

    /* 4. Circles Grid */
    .section-title-center { text-align: center; font-weight: 800; margin-bottom: 40px; letter-spacing: 1px; }
    .categories-circles-grid {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
    }
    .circle-cat {
        text-align: center;
        text-decoration: none;
        color: #333;
        width: 150px;
    }
    .circle-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        margin-bottom: 15px;
        transition: transform 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .circle-cat:hover .circle-img { transform: translateY(-5px); }
    .circle-cat span { font-weight: 700; font-size: 0.9rem; }
</style>

@endsection
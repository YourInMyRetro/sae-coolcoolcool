@extends('layout')

@section('content')

{{-- 1. MAIN HERO : L'affiche principale --}}
<section class="fifa-hero-fullscreen">
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
            <div class="visual-bg" style="background-image: url('https://digitalhub.fifa.com/transform/4e271296-678c-4861-9c6f-37013ba0c634/FIFA-Store-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="visual-content">
                <h3>HOMMES</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>

        {{-- Carte Femme --}}
        <a href="{{ route('produits.index', ['categorie' => 3]) }}" class="visual-card">
            <div class="visual-bg" style="background-image: url('https://digitalhub.fifa.com/transform/c2b83446-559d-407a-9694-554446c65c26/FWC22-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="visual-content">
                <h3>FEMMES</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>

        {{-- Carte Enfant --}}
        <a href="{{ route('produits.index', ['categorie' => 4]) }}" class="visual-card">
            <div class="visual-bg" style="background-image: url('https://digitalhub.fifa.com/transform/074e5083-f77c-4742-8703-b0972cb74026/FWC26-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <div class="visual-content">
                <h3>ENFANTS</h3>
                <span class="fake-link">Acheter</span>
            </div>
        </a>
    </div>
</div>

{{-- 3. BANNIÈRE ÉVÉNEMENT (COUPE DU MONDE) --}}
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
            <div class="circle-img" style="background-image: url('https://digitalhub.fifa.com/transform/85790483-34b7-4486-9304-7a3536869d5d/Fifa-Store-Generic-Promo-Block-Desktop?io=transform:fill,width:780,height:440&quality=75');"></div>
            <span>MAILLOTS</span>
        </a>
        <a href="{{ route('produits.index', ['categorie' => 1]) }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('https://digitalhub.fifa.com/transform/3b955b75-5214-4719-9588-f4b740921212/FWC26_Pattern_Blue_Green?io=transform:fill,width:400,height:400&quality=75');"></div>
            <span>ENTRAINEMENT</span>
        </a>
        <a href="{{ route('produits.index', ['categorie' => 3]) }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('https://digitalhub.fifa.com/transform/c2b83446-559d-407a-9694-554446c65c26/FWC22-Generic-Promo-Block-Desktop?io=transform:fill,width:400,height:400&quality=75');"></div>
            <span>ACCESSOIRES</span>
        </a>
        <a href="{{ route('produits.index') }}" class="circle-cat">
            <div class="circle-img" style="background-image: url('https://digitalhub.fifa.com/transform/074e5083-f77c-4742-8703-b0972cb74026/FWC26-Generic-Promo-Block-Desktop?io=transform:fill,width:400,height:400&quality=75');"></div>
            <span>CADEAUX</span>
        </a>
    </div>
</div>

@endsection
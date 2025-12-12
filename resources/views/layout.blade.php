<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Store Officiel - La Boutique des Fans</title>
    
    {{-- CSS Principal --}}
    <link rel="stylesheet" href="{{ asset('css/fifa-style.css') }}">
    
    {{-- FontAwesome pour les icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Police officielle Google Fonts (Open Sans) --}}
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    {{-- HEADER PREMIUM FIFA STYLE --}}
    <header class="fifa-header">
        <div class="container header-inner">
            
            {{-- BLOC GAUCHE : LOGO --}}
            <div class="header-left">
                <a href="{{ route('home') }}" class="logo-link">
                    {{-- Logo Textuel FIFA --}}
                    <span class="fifa-logo-text">FIFA STORE</span>
                </a>
            </div>

            {{-- BLOC CENTRE : NAVIGATION --}}
            <div class="header-center">
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                        <li><a href="{{ route('produits.index') }}" class="{{ request()->routeIs('produits.index') ? 'active' : '' }}">Boutique</a></li>
                        
                       {{-- --- AJOUT DU LIEN VOTE ICI --- --}}
                        <li><a href="{{ route('vote.index') }}" class="{{ request()->routeIs('vote.*') ? 'active' : '' }}">Votes</a></li>

                        {{-- --- BLOC DIRECTEUR (SEULEMENT POUR LE BOSS) --- --}}
                        @auth
                            @if(Auth::user()->isDirector())
                                <li>
                                    <a href="{{ route('directeur.dashboard') }}" style="color: #e74c3c; font-weight: bold;">
                                        <i class="fas fa-lock"></i> DIRECTION
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @auth
                            {{-- Menu spécifique au Service Expédition (Midas - Sprint 3) --}}
                            @if(Auth::user()->isExpedition())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        🚚 Service Expédition
                                    </a>
                                    <ul class="dropdown-menu">
                                        {{-- ID 25 & 26 : Consulter les commandes à livrer --}}
                                        <li>
                                            <a class="dropdown-item" href="{{ route('service.expedition') }}">
                                                Tableau de bord (Départs)
                                            </a>
                                        </li>
                                        {{-- ID 27 & 28 sont gérés DANS le tableau de bord, pas besoin de sous-menu --}}
                                    </ul>
                                </li>
                            @endif
                        @endauth

                        {{-- Exemples de filtres rapides --}}
                        <li><a href="{{ route('produits.index', ['categorie' => 1]) }}">Maillots</a></li>
                        <!--<li><a href="{{ route('matcher.index') }}">Matcher</a></li>-->
                    </ul>
                </nav>
            </div>

            {{-- BLOC DROITE : RECHERCHE & PANIER & USER --}}
            <div class="header-right">
                
                {{-- Barre de recherche intégrée --}}
                <form action="{{ route('produits.index') }}" method="GET" class="search-bar-container">
                    <input type="text" name="search" class="search-input" placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>

                {{-- Icônes Utilisateur & Panier --}}
                <div class="header-icons">
                    
                    {{-- 1. LE BONHOMME (Logique Connexion / Compte) --}}
                    @guest
                        {{-- Si VISITEUR : Lien vers Login --}}
                        <a href="{{ route('login') }}" class="icon-btn" title="Se connecter">
                            <i class="far fa-user"></i>
                        </a>
                    @endguest

                    @auth
                        {{-- Si CONNECTÉ : Lien vers Compte (Icône pleine + couleur distinctive) --}}
                        <a href="{{ route('compte.index') }}" class="icon-btn" title="Mon Compte">
                            <i class="fas fa-user" style="color: #55e6c1;"></i>
                        </a>
                    @endauth

                    {{-- 2. LE PANIER --}}
                    <a href="{{ route('panier.index') }}" class="icon-btn cart-btn-wrapper" title="Mon Panier">
                        <i class="fas fa-shopping-bag"></i>
                        @if(count(session('panier', [])) > 0)
                            <span class="cart-badge">{{ count(session('panier', [])) }}</span>
                        @endif
                    </a>

                </div>
            </div>

        </div>
    </header>

    {{-- Espaceur pour compenser le header fixe (si tu as mis position:fixed en CSS) --}}
    <div class="header-spacer"></div>

    {{-- Messages Flash (Succès/Erreur) --}}
    @if(session('success'))
        <div class="alert-success-fifa" style="background: #55e6c1; color: #101010; padding: 15px; text-align: center; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-fifa" style="background: #e74c3c; color: white; padding: 15px; text-align: center; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- CONTENU PRINCIPAL --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="fifa-footer">
        <div class="container footer-content">
            <div class="footer-logo">
                <h2>FIFA STORE</h2>
            </div>
            <div class="footer-links">
                <a href="#">Confidentialité</a>
                <a href="#">Conditions d'utilisation</a>
                <a href="#">Cookies</a>
            </div>
            <p class="copyright">&copy; 2025 FIFA. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>
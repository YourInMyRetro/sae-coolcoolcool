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
                    {{-- Si tu as une image logo, remplace le texte ci-dessous par <img src="..." /> --}}
                    <span class="fifa-logo-text">FIFA STORE</span>
                </a>
            </div>

            {{-- BLOC CENTRE : NAVIGATION --}}
            <div class="header-center">
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                        <li><a href="{{ route('produits.index') }}" class="{{ request()->routeIs('produits.index') ? 'active' : '' }}">Boutique</a></li>
                        <li><a href="{{ route('produits.index', ['categorie' => 1]) }}">Maillots</a></li>
                        <li><a href="{{ route('produits.index', ['categorie' => 3]) }}">Accessoires</a></li>
                    </ul>
                </nav>
            </div>

            {{-- BLOC DROITE : RECHERCHE & PANIER --}}
            <div class="header-right">
                
                {{-- Barre de recherche intégrée --}}
                <form action="{{ route('produits.index') }}" method="GET" class="search-bar-container">
                    <input type="text" name="search" class="search-input" placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>

                {{-- Icônes Utilisateur & Panier --}}
                <div class="header-icons">
                    <a href="#" class="icon-btn" title="Mon Compte"><i class="far fa-user"></i></a>
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

    {{-- Espaceur pour compenser le header fixe --}}
    <div class="header-spacer"></div>

    {{-- Messages Flash (Succès/Erreur) --}}
    @if(session('success'))
        <div class="alert-success-fifa">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
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
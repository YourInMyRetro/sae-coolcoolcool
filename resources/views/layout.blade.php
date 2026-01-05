<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Store Officiel - La Boutique des Fans</title>
    
    <link rel="stylesheet" href="{{ asset('css/fifa-style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <header class="fifa-header">
        <div class="container header-inner">
            
            <div class="header-left">
                <a href="{{ route('home') }}" class="logo-link">
                    <span class="fifa-logo-text">FIFA STORE</span>
                </a>
            </div>

            <div class="header-center">
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                        <li><a href="{{ route('produits.index') }}" class="{{ request()->routeIs('produits.index') ? 'active' : '' }}">Boutique</a></li>
                        
                        <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Actualités</a></li>

                        <li><a href="{{ route('vote.index') }}" class="{{ request()->routeIs('vote.*') ? 'active' : '' }}">Votes</a></li>

                        @auth
                            @if(Auth::user()->role === 'service_vente')
                                <li>
                                    <a href="{{ route('vente.dashboard') }}" 
                                       class="{{ request()->routeIs('vente.*') ? 'active' : '' }}"
                                       style="color: #ffd700; font-weight: bold; border: 1px solid #ffd700; padding: 5px 10px; border-radius: 4px;">
                                       <i class="fas fa-tools"></i> SERVICE VENTE
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @auth
                            @if(Auth::user()->isServiceCommande())
                                <li>
                                    <a href="{{ route('service.dashboard') }}" 
                                       class="{{ request()->routeIs('service.dashboard') ? 'active' : '' }}"
                                       style="color: #9b59b6; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                                       <i class="fas fa-headset"></i> SAV
                                    </a>
                                </li>
                            @endif
                        @endauth

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
                            @if(Auth::user()->isExpedition())
                                <li>
                                    <a href="{{ route('service.expedition') }}" 
                                    class="{{ request()->routeIs('service.expedition') ? 'active' : '' }}" 
                                    style="color: #e67e22; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-truck"></i> EXPÉDITION
                                    </a>
                                </li>
                            @endif
                        @endauth

                        <li><a href="{{ route('produits.index', ['categorie' => 1]) }}">Maillots</a></li>
                    </ul>
                </nav>
            </div>

            <div class="header-right">
                
                <form action="{{ route('produits.index') }}" method="GET" class="search-bar-container">
                    <input type="text" name="search" class="search-input" placeholder="Rechercher..." value="{{ request('search') }}">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>

                <div class="header-icons">
                    
                    @guest
                        <a href="{{ route('login') }}" class="icon-btn" title="Se connecter">
                            <i class="far fa-user"></i>
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('compte.index') }}" class="icon-btn" title="Mon Compte">
                            <i class="fas fa-user" style="color: #55e6c1;"></i>
                        </a>
                    @endauth

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

    <div class="header-spacer"></div>

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

    <main>
        @yield('content')
    </main>

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

    <div id="cookie-banner" style="position: fixed; bottom: 0; left: 0; right: 0; background-color: #333; color: white; padding: 20px; text-align: center; display: none; z-index: 9999; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
        <p style="margin-bottom: 10px;">Nous utilisons des cookies pour améliorer votre expérience sur le FIFA Store. Acceptez-vous les cookies ?</p>
        <button onclick="acceptCookies()" style="background-color: #55e6c1; border: none; padding: 10px 20px; cursor: pointer; color: black; font-weight: bold; border-radius: 4px;">Accepter</button>
        <button onclick="refuseCookies()" style="background-color: #e74c3c; border: none; padding: 10px 20px; cursor: pointer; color: white; font-weight: bold; margin-left: 10px; border-radius: 4px;">Refuser</button>
    </div>

    <script>
        function checkCookies() {
            if (!localStorage.getItem('cookie_consent')) {
                document.getElementById('cookie-banner').style.display = 'block';
            }
        }

        function acceptCookies() {
            localStorage.setItem('cookie_consent', 'accepted');
            document.getElementById('cookie-banner').style.display = 'none';
        }

        function refuseCookies() {
            localStorage.setItem('cookie_consent', 'refused');
            document.getElementById('cookie-banner').style.display = 'none';
        }

        window.onload = checkCookies;
    </script>

    <a href="{{ route('chat.index') }}" 
       style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background-color: #00cfb7; color: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; text-decoration: none; transition: transform 0.3s;"
       onmouseover="this.style.transform='scale(1.1)'" 
       onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-comments fa-2x"></i>
    </a>

</body>
</html>
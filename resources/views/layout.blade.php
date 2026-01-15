<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Store Officiel - La Boutique des Fans</title>
    
    <link rel="stylesheet" href="{{ asset('css/fifa-style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- STYLE DU SYSTÈME D'AIDE (TOOLTIPS) --}}
    <style>
        /* Le point d'interrogation ou l'icône déclencheur */
        .help-trigger {
            color: #00cfb7; /* Couleur FIFA Turquoise */
            cursor: pointer;
            margin-left: 5px;
            font-size: 0.9em;
            transition: all 0.2s ease;
            display: inline-block;
            vertical-align: middle;
            position: relative;
            z-index: 10;
        }

        .help-trigger:hover {
            transform: scale(1.3);
            color: #00ff87;
            filter: drop-shadow(0 0 5px rgba(0,255,135,0.5));
        }

        /* L'écran sombre semi-transparent */
        .help-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Assombri le fond */
            backdrop-filter: blur(2px); /* Flou artistique */
            z-index: 99998; /* Très haut */
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* La bulle d'aide (Popup) */
        .help-popup {
            position: absolute;
            background: white;
            border-top: 5px solid #326295; /* Bordure bleue en haut */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 99999; /* Au-dessus de l'overlay */
            display: none;
            width: 350px; /* Largeur confortable */
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }

        .help-popup h4 {
            margin-top: 0;
            color: #326295;
            font-size: 1.1rem;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .help-popup p {
            margin-bottom: 15px;
        }

        .help-popup .btn-see-more {
            display: inline-block;
            background-color: #326295;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-decoration: none;
            float: right;
            transition: background 0.2s;
        }

        .help-popup .btn-see-more:hover {
            background-color: #244a75;
        }
        
        /* Petite flèche CSS */
        .help-popup::after {
            content: '';
            position: absolute;
            top: -10px;
            left: 20px;
            border-width: 0 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #326295 transparent;
        }
        
        /* Ajustement flèche si la bulle est à droite */
        .help-popup.right-aligned::after {
            left: auto;
            right: 20px;
        }
    </style>
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

                        <li>
                            <a href="{{ route('aide') }}" class="{{ request()->routeIs('aide') ? 'active' : '' }}" style="color: #00cfb7;">
                                <i class="fas fa-question-circle"></i> AIDE
                            </a>
                        </li>
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

    {{-- Chat Flottant --}}
    <a href="{{ route('chat.index') }}" 
       style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background-color: #00cfb7; color: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; text-decoration: none; transition: transform 0.3s;"
       onmouseover="this.style.transform='scale(1.1)'" 
       onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-comments fa-2x"></i>
    </a>

    {{-- Overlay pour le système d'aide (Ajouté) --}}
    <div id="global-help-overlay" class="help-overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        // Activation des infobulles Bootstrap (si utilisées ailleurs)
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        // ==========================================
        // SYSTEME DE POPUPS D'AIDE INTELLIGENT
        // ==========================================
        document.addEventListener('DOMContentLoaded', function () {
            const triggers = document.querySelectorAll('.help-trigger');
            const overlay = document.getElementById('global-help-overlay');
            let activePopup = null;

            function closePopup() {
                if (activePopup) {
                    activePopup.remove();
                    activePopup = null;
                }
                overlay.style.opacity = '0';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }

            triggers.forEach(trigger => {
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (activePopup) closePopup();

                    // Récupération des données
                    const title = this.getAttribute('data-title');
                    const content = this.getAttribute('data-content');
                    const link = this.getAttribute('data-link');

                    // Création de la popup
                    const popup = document.createElement('div');
                    popup.className = 'help-popup';
                    
                    let htmlContent = `<h4><i class="fas fa-info-circle"></i> ${title}</h4><p>${content}</p>`;
                    if (link) {
                        htmlContent += `<a href="${link}" class="btn-see-more">Voir la FAQ complète &rarr;</a>`;
                    }
                    
                    popup.innerHTML = htmlContent;
                    document.body.appendChild(popup);

                    // Positionnement
                    const rect = this.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

                    // On positionne 15px sous l'élément cliqué
                    popup.style.top = (rect.bottom + scrollTop + 15) + 'px';
                    
                    // Si on est à droite de l'écran, on aligne à droite pour ne pas déborder
                    if (rect.left > window.innerWidth / 2) {
                        popup.style.left = (rect.left + scrollLeft - 300) + 'px'; // 300px = largeur approx de la popup
                        popup.classList.add('right-aligned');
                    } else {
                        popup.style.left = (rect.left + scrollLeft - 20) + 'px';
                    }
                    
                    // Affichage
                    popup.style.display = 'block';
                    overlay.style.display = 'block';
                    setTimeout(() => overlay.style.opacity = '1', 10);
                    
                    activePopup = popup;
                });
            });

            overlay.addEventListener('click', closePopup);
            document.addEventListener('keydown', function(e) {
                if (e.key === "Escape") closePopup();
            });
        });
    </script>

    @include('partials.cinematic_transition')
    @include('partials.hyper_transition')
</body>
</html>
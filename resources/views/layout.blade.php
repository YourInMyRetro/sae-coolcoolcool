<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Store Officiel</title>
    
    <link rel="stylesheet" href="{{ asset('css/fifa-style.css') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header>
        <a href="{{ route('home') }}" class="logo">
            FIFA STORE
        </a>

        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="nav-link">Accueil</a>
            <a href="{{ route('produits.index') }}" class="nav-link">Boutique</a>
            
            <form action="{{ route('produits.index') }}" method="GET" class="header-search">
                <button type="submit"><i class="fas fa-search"></i></button>
                <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
            </form>

            <a href="{{ route('panier.index') }}" class="cart-btn">
                <i class="fas fa-shopping-bag"></i>
                @if(count(session('panier', [])) > 0)
                    <span class="cart-badge">{{ count(session('panier', [])) }}</span>
                @endif
            </a>
        </nav>
    </header>

    @if(session('success'))
        <div style="background: var(--fifa-cyan); color: var(--fifa-dark); text-align: center; padding: 10px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')

    <footer>
        <p>&copy; 2025 FIFA. Tous droits réservés.</p>
    </footer>

</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Store Officiel</title>
    <link rel="stylesheet" href="{{ asset('css/fifa-style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <a href="{{ route('home') }}" class="logo">
            <i class="fas fa-futbol"></i> FIFA
        </a>

        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="nav-link">Accueil</a>
            <a href="#" class="nav-link">Compétitions</a>
            <a href="#" class="nav-link">Billetterie</a>
            
            <a href="{{ route('produits.index') }}" class="btn-store">
                FIFA Store <i class="fas fa-shopping-bag"></i>
            </a>
        </nav>
    </header>

    @yield('content')

    <footer style="background: #0f2d4a; color: white; padding: 40px; text-align: center; margin-top: 50px;">
        <p>&copy; 2025 FIFA. Tous droits réservés.</p>
    </footer>

</body>
</html>
@extends('layout')

@section('content')
<div class="account-page-wrapper">
    <div class="account-container">
        
        <div class="account-header">
            <div>
                <h1>Mon Espace</h1>
                <p style="color: #888;">Gérez vos informations et vos commandes</p>
            </div>
            <div class="user-badge">
                <i class="fas fa-check-circle"></i> Membre Connecté
            </div>
        </div>

        @if(session('success'))
            <div style="background: rgba(0, 255, 135, 0.2); color: #00ff87; padding: 15px; border: 1px solid #00ff87; border-radius: 5px; margin-bottom: 30px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="account-grid">
            <div class="account-card">
                <h2><i class="far fa-id-card"></i> Mes Infos</h2>
                <ul>
                    <li><strong>Nom :</strong> {{ $utilisateur->nom }}</li>
                    <li><strong>Prénom :</strong> {{ $utilisateur->prenom }}</li>
                    <li><strong>Email :</strong> {{ $utilisateur->mail }}</li>
                    <li><strong>Pays :</strong> {{ $utilisateur->pays_naissance }}</li>
                </ul>
                <a href="{{ route('compte.edit') }}" class="btn-account-action">Modifier mon profil</a>
            </div>

            @if($estPro)
            <div class="account-card" style="border-color: #00d4ff;">
                <h2 style="color: #00d4ff;"><i class="fas fa-briefcase"></i> Espace Pro</h2>
                <ul>
                    <li><strong>Société :</strong> {{ $infosPro->nom_societe }}</li>
                    <li><strong>Activité :</strong> {{ $infosPro->activite }}</li>
                    <li><strong>TVA :</strong> {{ $infosPro->numero_tva_intracommunautaire }}</li>
                </ul>
                <div style="margin-top: 10px; font-size: 0.8rem; color: #00d4ff;">
                    Statut : Vérifié <i class="fas fa-check"></i>
                </div>
            </div>
            @endif

            <div class="account-card">
                <h2><i class="fas fa-cog"></i> Paramètres</h2>
                <p style="color: #aaa; margin-bottom: 20px;">Gérez la sécurité de votre compte.</p>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Se déconnecter
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
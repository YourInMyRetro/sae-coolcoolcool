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

        @if(session('error'))
            <div style="background: rgba(255, 99, 71, 0.2); color: #ff6347; padding: 15px; border: 1px solid #ff6347; border-radius: 5px; margin-bottom: 30px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="account-grid">
            {{-- CARTE 1 : MES INFOS --}}
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

            {{-- CARTE 2 : MES COMMANDES (NOUVEAU) --}}
            <div class="account-card">
                <h2><i class="fas fa-box-open"></i> Mes Commandes</h2>
                <p style="color: #aaa; margin-bottom: 20px;">Retrouvez l'historique de vos achats et suivez vos livraisons en cours.</p>
                
                <a href="{{ route('compte.commandes') }}" class="btn-account-action" style="background-color: #326295; color: white; border: none;">
                    <i class="fas fa-list-ul"></i> Suivi de mes commandes
                </a>
            </div>

            {{-- CARTE 3 : ESPACE PRO (Si applicable) --}}
            @if($estPro)
            <div class="account-card" style="border-color: #00d4ff;">
                <h2 style="color: #00d4ff;"><i class="fas fa-briefcase"></i> Espace Pro</h2>
                <ul>
                    <li><strong>Société :</strong> {{ $infosPro->nom_societe }}</li>
                    <li><strong>Activité :</strong> {{ $infosPro->activite }}</li>
                    <li><strong>TVA :</strong> {{ $infosPro->numero_tva_intracommunautaire }}</li>
                </ul>
                
                <a href="{{ route('compte.demande.create') }}" class="btn-account-action" style="color: #00d4ff; border-color: #00d4ff;">
                    <i class="fas fa-plus-circle"></i> Nouvelle demande produit
                </a>
            </div>
            
            <div class="account-card" style="grid-column: span 2; border-color: #00d4ff;">
                <h2 style="color: #00d4ff;"><i class="fas fa-file-contract"></i> Mes Demandes Spéciales</h2>
                @if(count($mesDemandes) > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; color: #ccc;">
                        <thead>
                            <tr style="border-bottom: 1px solid #444; color: #00d4ff;">
                                <th style="text-align: left; padding: 10px;">Date</th>
                                <th style="text-align: left; padding: 10px;">Sujet</th>
                                <th style="text-align: left; padding: 10px;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mesDemandes as $d)
                            <tr style="border-bottom: 1px solid #333;">
                                <td style="padding: 10px;">{{ \Carbon\Carbon::parse($d->date_demande)->format('d/m/Y') }}</td>
                                <td style="padding: 10px;">{{ $d->sujet }}</td>
                                <td style="padding: 10px;">
                                    @if($d->statut == 'En attente')
                                        <span style="color: orange;">{{ $d->statut }}</span>
                                    @else
                                        <span style="color: #00ff87;">{{ $d->statut }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #777; margin-top: 15px;">Aucune demande de conception en cours.</p>
                @endif
            </div>
            @endif

            {{-- CARTE 4 : PARAMÈTRES --}}
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
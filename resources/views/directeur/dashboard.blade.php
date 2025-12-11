@extends('layout')

@section('content')
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h6 class="text-uppercase text-muted fw-bold mb-1">Espace Direction</h6>
            <h1 class="text-white fw-bold display-5">Tableau de Bord</h1>
        </div>
        
        <a href="{{ route('directeur.produits_incomplets') }}" class="btn-fifa-action">
            <i class="fas fa-tools me-2"></i> Produits à valider
            @if(isset($nbProduitsIncomplets) && $nbProduitsIncomplets > 0)
                <span class="badge-count">{{ $nbProduitsIncomplets }}</span>
            @endif
        </a>
    </div>

    @if($ventesMensuelles->isEmpty())
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i> Aucune donnée de vente disponible.
        </div>
    @else
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="dashboard-card d-flex align-items-center justify-content-between">
                    <div>
                        <h3>Chiffre d'Affaires ({{ $ventesMensuelles->first()->mois }})</h3>
                        <div class="stat-value">{{ number_format($ventesMensuelles->first()->total_ventes, 2, ',', ' ') }} €</div>
                        <p class="mb-0 text-success"><i class="fas fa-arrow-up"></i> Mois en cours</p>
                    </div>
                    <div class="icon-bg text-muted opacity-25">
                        <i class="fas fa-wallet fa-4x"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card d-flex align-items-center justify-content-between">
                    <div>
                        <h3>Top Catégorie ({{ $ventesParCategorie->first()->mois }})</h3>
                        <div class="stat-value text-info">{{ $ventesParCategorie->first()->nom_categorie }}</div>
                        <p class="mb-0 text-white">{{ number_format($ventesParCategorie->first()->total, 2, ',', ' ') }} € générés</p>
                    </div>
                    <div class="icon-bg text-muted opacity-25">
                        <i class="fas fa-trophy fa-4x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="dashboard-card h-auto">
                    <h3 class="border-bottom pb-3 mb-4 border-secondary">Historique Mensuel</h3>
                    <table class="fifa-table">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th class="text-end">Montant Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventesMensuelles as $vente)
                            <tr>
                                <td>{{ $vente->mois }}</td>
                                <td class="text-end">{{ number_format($vente->total_ventes, 2, ',', ' ') }} €</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="dashboard-card h-auto">
                    <h3 class="border-bottom pb-3 mb-4 border-secondary">Performance par Catégorie</h3>
                    <table class="fifa-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Mois</th>
                                <th class="text-end">CA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventesParCategorie->take(6) as $row)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $row->nom_categorie }}</span></td>
                                <td class="text-muted small">{{ $row->mois }}</td>
                                <td class="text-end">{{ number_format($row->total, 2, ',', ' ') }} €</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
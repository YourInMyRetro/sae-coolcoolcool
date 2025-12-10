@extends('layout')

@section('content')
<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-uppercase fw-bold text-primary mb-0">Tableau de Bord Direction</h1>
        
        <a href="{{ route('directeur.produits_incomplets') }}" class="btn btn-danger btn-lg shadow">
            <i class="fas fa-tools me-2"></i> Gérer les produits manquants
            @if(isset($nbProduitsIncomplets) && $nbProduitsIncomplets > 0)
                <span class="badge bg-white text-danger ms-2">{{ $nbProduitsIncomplets }}</span>
            @endif
        </a>
    </div>

    @if($ventesMensuelles->isEmpty())
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i> Aucune vente détectée dans la base de données.
        </div>
    @else
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white">
                <h3 class="h5 mb-0">Chiffre d'Affaires Mensuel (US 29)</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Mois</th>
                            <th class="text-end">CA Total (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventesMensuelles as $vente)
                        <tr>
                            <td>{{ $vente->mois }}</td>
                            <td class="text-end fw-bold">{{ number_format($vente->total_ventes, 2, ',', ' ') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-info text-dark">
                <h3 class="h5 mb-0">Ventes par Catégorie (US 30)</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-secondary">
                            <th>Mois</th>
                            <th>Catégorie</th>
                            <th class="text-end">CA (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventesParCategorie as $row)
                        <tr>
                            <td>{{ $row->mois }}</td>
                            <td>{{ $row->nom_categorie }}</td>
                            <td class="text-end">{{ number_format($row->total, 2, ',', ' ') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
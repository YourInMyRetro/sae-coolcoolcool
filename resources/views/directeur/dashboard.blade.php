@extends('layout')

@section('content')
<div class="container mt-5">
    <h1 class="text-uppercase fw-bold text-primary mb-4">Tableau de Bord Direction</h1>

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
                    <tr>
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
</div>
@endsection
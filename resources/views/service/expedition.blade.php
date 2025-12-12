@extends('layout')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">🚛 Dashboard Service Expédition</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Formulaire global pour les actions de groupe --}}
    <form action="{{ route('service.expedition.pickup') }}" method="POST">
        @csrf

        {{-- ID 25 : Transport à Domicile (Demi-journée prochaine) --}}
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    🏠 Transport à Domicile (Standard)
                </h5>
                <span class="badge bg-light text-primary">Créneau : {{ $creneauDomicile }}</span>
            </div>
            <div class="card-body">
                @if($commandesDomicile->isEmpty())
                    <p class="text-muted">Aucune commande standard à préparer pour ce créneau.</p>
                @else
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="checkAllDomicile" onclick="toggleAll('Domicile')">
                                </th>
                                <th>N° Com.</th>
                                <th>Date</th>
                                <th>Client / Tél</th>
                                <th>Montant</th>
                                <th>Action (ID 28)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandesDomicile as $c)
                            <tr>
                                <td>
                                    <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input checkbox-Domicile">
                                </td>
                                <td><strong>#{{ $c->id_commande }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m H:i') }}</td>
                                <td>
                                    {{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}<br>
                                    <small class="text-muted">📞 {{ $c->utilisateur->telephone ?? 'Non renseigné' }}</small>
                                </td>
                                <td>{{ number_format($c->montant_total, 2) }} €</td>
                                <td>
                                    {{-- Bouton SMS (ID 28) --}}
                                    <button type="submit" form="sms-form-{{ $c->id_commande }}" class="btn btn-sm btn-outline-info" title="Envoyer SMS">
                                        📱 SMS
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- ID 26 : Autre Mode (Journée prochaine) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    ⚡ Autre Mode / Express
                </h5>
                <span class="badge bg-dark text-warning">Créneau : {{ $creneauAutre }}</span>
            </div>
            <div class="card-body">
                @if($commandesAutre->isEmpty())
                    <p class="text-muted">Aucune commande express à préparer.</p>
                @else
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="checkAllAutre" onclick="toggleAll('Autre')">
                                </th>
                                <th>N° Com.</th>
                                <th>Date</th>
                                <th>Client / Tél</th>
                                <th>Montant</th>
                                <th>Action (ID 28)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandesAutre as $c)
                            <tr>
                                <td>
                                    <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input checkbox-Autre">
                                </td>
                                <td><strong>#{{ $c->id_commande }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m H:i') }}</td>
                                <td>
                                    {{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}<br>
                                    <small class="text-muted">📞 {{ $c->utilisateur->telephone ?? 'Non renseigné' }}</small>
                                </td>
                                <td>{{ number_format($c->montant_total, 2) }} €</td>
                                <td>
                                    <button type="submit" form="sms-form-{{ $c->id_commande }}" class="btn btn-sm btn-outline-info" title="Envoyer SMS">
                                        📱 SMS
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- ID 27 : Bouton Global de Prise en Charge --}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
            <button type="submit" class="btn btn-success btn-lg">
                🚚 Valider la prise en charge (Commandes cochées)
            </button>
        </div>

    </form>

    {{-- Formulaires cachés pour les SMS individuels --}}
    @foreach($commandesDomicile->merge($commandesAutre) as $c)
        <form id="sms-form-{{ $c->id_commande }}" action="{{ route('service.expedition.sms', $c->id_commande) }}" method="POST" class="d-none">
            @csrf
        </form>
    @endforeach

</div>

<script>
    // Petit script pour cocher/décocher tout par section
    function toggleAll(type) {
        const master = document.getElementById('checkAll' + type);
        const checkboxes = document.getElementsByClassName('checkbox-' + type);
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = master.checked;
        }
    }
</script>
@endsection
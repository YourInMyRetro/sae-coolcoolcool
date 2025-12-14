@extends('layout')

@section('content')
{{-- WRAPPER GLOBAL : Fond gris pleine largeur --}}
<div class="w-100 py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    
    {{-- CONTENEUR CENTRÉ : Pour s'aligner parfaitement avec le Header --}}
    <div class="container">

        {{-- 1. HEADER & KPI (Indicateurs Clés) --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">📦 Pilotage Logistique</h1>
                <p class="text-muted mb-0 small">Supervision des départs et enlèvements transporteurs.</p>
            </div>
            {{-- Barre de recherche rapide --}}
            <div class="d-none d-md-block" style="width: 300px;">
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="globalSearch" class="form-control border-start-0 ps-0" placeholder="Rechercher une commande...">
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- KPI EXPRESS / URGENT (ROUGE = CRITIQUE) --}}
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-uppercase fw-bold text-danger mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Prioritaire / Express</h6>
                                <div class="display-4 fw-bold text-dark">{{ count($commandesAutre) }}</div>
                                <div class="text-muted small mt-1">Colis à expédier avant <strong>{{ str_replace('Demain ', '', $creneauAutre) }}</strong></div>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-shipping-fast fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI STANDARD (BLEU = FLUX NORMAL) --}}
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-uppercase fw-bold text-primary mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Flux Standard</h6>
                                <div class="display-4 fw-bold text-dark">{{ count($commandesDomicile) }}</div>
                                <div class="text-muted small mt-1">Enlèvement prévu : <strong>{{ $creneauDomicile }}</strong></div>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI OUTILS --}}
            <div class="col-md-6 col-lg-4 d-none d-lg-block">
                <div class="card border-0 shadow-sm h-100 bg-white">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem;">Actions Rapides</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-dark fw-bold"><i class="fas fa-print me-1"></i> Étiquettes</button>
                            <button class="btn btn-sm btn-outline-dark"><i class="fas fa-history me-1"></i> Historique</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MESSAGES FLASH --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="fas fa-check-circle fs-4 me-3"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
        @endif

        <form action="{{ route('service.expedition.pickup') }}" method="POST">
            @csrf

            {{-- ZONE 1 : URGENCES (Tableau Détaillé) --}}
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold text-danger mb-0"><i class="fas fa-exclamation-circle me-2"></i>Départs Urgents (Express)</h5>
                    @if($commandesAutre->count() > 0)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAllAutre" onclick="toggleAll('Autre')" style="cursor: pointer;">
                            <label class="form-check-label fw-bold small text-muted user-select-none" for="checkAllAutre">Tout sélectionner</label>
                        </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover search-table">
                        <thead class="bg-light text-uppercase text-muted small">
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th>Référence</th>
                                <th>Date Création</th>
                                <th>Client & Destination</th>
                                <th>Transporteur</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandesAutre as $c)
                            <tr style="background-color: #fff5f5;"> {{-- Fond très légèrement rouge --}}
                                <td class="ps-4">
                                    <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input checkbox-Autre fs-5 border-danger" style="cursor: pointer;">
                                </td>
                                <td>
                                    <span class="badge bg-danger text-white">#{{ $c->id_commande }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($c->date_commande)->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</span>
                                        @if(!$c->utilisateur->telephone)
                                            <span class="badge bg-warning text-dark ms-2" title="Numéro manquant"><i class="fas fa-phone-slash"></i> Sans Tél</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted searchable-text"><i class="fas fa-map-marker-alt me-1"></i> {{ $c->adresse->ville_adresse ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">{{ $c->type_livraison }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" onclick="document.getElementById('sms-form-{{ $c->id_commande }}').submit();" class="btn btn-sm btn-white border shadow-sm text-primary fw-bold">
                                        <i class="fas fa-sms me-1"></i> SMS
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i><br>
                                    Aucune urgence.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ZONE 2 : STANDARD (Tableau Détaillé) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold text-primary mb-0"><i class="fas fa-shipping-fast me-2"></i>Départs Standard</h5>
                    @if($commandesDomicile->count() > 0)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAllDomicile" onclick="toggleAll('Domicile')" style="cursor: pointer;">
                            <label class="form-check-label fw-bold small text-muted user-select-none" for="checkAllDomicile">Tout sélectionner</label>
                        </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover search-table">
                        <thead class="bg-light text-uppercase text-muted small">
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th>Référence</th>
                                <th>Date Création</th>
                                <th>Client & Contact</th>
                                <th>Statut</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandesDomicile as $c)
                            <tr>
                                <td class="ps-4">
                                    <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input checkbox-Domicile fs-5" style="cursor: pointer;">
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">#{{ $c->id_commande }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($c->date_commande)->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</div>
                                    @if($c->utilisateur->telephone)
                                        <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $c->utilisateur->telephone }}</small>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger"><i class="fas fa-exclamation-triangle"></i> Sans Tél</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">En préparation</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" onclick="document.getElementById('sms-form-{{ $c->id_commande }}').submit();" class="btn btn-sm btn-light border text-muted" title="Notifier le client">
                                        <i class="fas fa-comment-dots"></i> SMS
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 opacity-25"></i><br>
                                    Aucun colis standard en attente.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ACTION DE MASSE FLOTTANTE --}}
            <div class="fixed-bottom bg-white border-top shadow-lg py-3" style="z-index: 1050;">
                <div class="container d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                        <span class="d-none d-md-inline">Sélectionnez les commandes remises au transporteur pour valider l'expédition.</span>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg fw-bold px-5 shadow rounded-pill">
                        <i class="fas fa-check-double me-2"></i> VALIDER L'ENLÈVEMENT
                    </button>
                </div>
            </div>
            
            <div style="height: 100px;"></div> {{-- Spacer pour le footer --}}

        </form>

        {{-- Formulaires Cachés (SMS) --}}
        @foreach($commandesDomicile->merge($commandesAutre) as $c)
            <form id="sms-form-{{ $c->id_commande }}" action="{{ route('service.expedition.sms', $c->id_commande) }}" method="POST" class="d-none">@csrf</form>
        @endforeach

    </div>
</div>

<script>
    // 1. Fonction pour cocher tout (par section)
    function toggleAll(type) {
        const master = document.getElementById('checkAll' + type);
        const checkboxes = document.getElementsByClassName('checkbox-' + type);
        for (let i = 0; i < checkboxes.length; i++) { checkboxes[i].checked = master.checked; }
    }

    // 2. Recherche Simple (Filtrage visuel)
    document.getElementById('globalSearch').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('.search-table tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? '' : 'none';
        });
    });
</script>
@endsection
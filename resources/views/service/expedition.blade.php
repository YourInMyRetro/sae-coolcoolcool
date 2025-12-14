@extends('layout')

@section('content')
{{-- Style in-line pour forcer le look "App" sans toucher au CSS global pour l'instant --}}
<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    .kpi-card { transition: transform 0.2s; border: none; border-radius: 12px; }
    .kpi-card:hover { transform: translateY(-3px); }
    .table-card { border-radius: 12px; border: none; overflow: hidden; }
    .table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; border-bottom: 2px solid #eaecf0; padding: 1rem; }
    .table tbody td { padding: 1rem; vertical-align: middle; color: #344767; font-size: 0.9rem; }
    .avatar-initials { width: 35px; height: 35px; background: #e9ecef; color: #495057; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 0.8rem; margin-right: 10px; }
    .status-badge { padding: 0.35em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
    .search-bar { border-radius: 50px; border: 1px solid #dee2e6; padding-left: 1.5rem; height: 50px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
    .floating-action-bar { background: white; border-top: 1px solid #dee2e6; box-shadow: 0 -4px 20px rgba(0,0,0,0.05); z-index: 1000; }
</style>

<div class="container py-5">

    {{-- 1. EN-TÊTE & RECHERCHE --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-5">
            <h1 class="h3 fw-bold text-dark mb-1">Pilotage Expéditions</h1>
            <p class="text-muted mb-0">Gestion des départs du {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-7">
            <div class="position-relative">
                <input type="text" id="globalSearch" class="form-control search-bar" placeholder="Rechercher un client, une référence, une ville...">
                <i class="fas fa-search text-muted position-absolute" style="right: 20px; top: 17px;"></i>
            </div>
        </div>
    </div>

    {{-- 2. KPI / WIDGETS (La vue d'ensemble) --}}
    <div class="row g-4 mb-5">
        {{-- KPI EXPRESS (Urgence) --}}
        <div class="col-md-6">
            <div class="card kpi-card shadow-sm h-100" style="background: linear-gradient(135deg, #fff 0%, #fff5f5 100%); border-left: 6px solid #dc3545;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-danger fw-bold small mb-2"><i class="fas fa-stopwatch me-1"></i> Prioritaire / Express</div>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 fw-bold text-dark me-2">{{ count($commandesAutre) }}</span>
                            <span class="text-muted">colis à traiter</span>
                        </div>
                        <div class="mt-2 badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                            <i class="fas fa-clock me-1"></i> Deadline : {{ str_replace('Demain ', '', $creneauAutre) }}
                        </div>
                    </div>
                    <div class="bg-white p-3 rounded-circle shadow-sm text-danger">
                        <i class="fas fa-shipping-fast fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI STANDARD (Flux normal) --}}
        <div class="col-md-6">
            <div class="card kpi-card shadow-sm h-100" style="background: linear-gradient(135deg, #fff 0%, #f0f7ff 100%); border-left: 6px solid #0d6efd;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-primary fw-bold small mb-2"><i class="fas fa-box me-1"></i> Standard / Domicile</div>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 fw-bold text-dark me-2">{{ count($commandesDomicile) }}</span>
                            <span class="text-muted">colis en attente</span>
                        </div>
                        <div class="mt-2 badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="fas fa-truck-loading me-1"></i> Enlèvement : {{ $creneauDomicile }}
                        </div>
                    </div>
                    <div class="bg-white p-3 rounded-circle shadow-sm text-primary">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MESSAGES FLASH --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 rounded-3">
            <i class="fas fa-check-circle fs-4 me-3"></i>
            <div class="fw-medium">{{ session('success') }}</div>
        </div>
    @endif

    <form action="{{ route('service.expedition.pickup') }}" method="POST" id="pickupForm">
        @csrf

        {{-- 3. LISTE DES EXPÉDITIONS (Tableau Unifié ou Séparé par Card) --}}
        
        {{-- BLOC 1 : URGENCES --}}
        <div class="card table-card shadow-sm mb-5">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-danger mb-0 d-flex align-items-center">
                        <span class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.9rem;"><i class="fas fa-bolt"></i></span>
                        File Prioritaire
                    </h5>
                    @if($commandesAutre->count() > 0)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAllAutre" onclick="toggleAll('Autre')">
                        <label class="form-check-label text-muted small fw-bold" for="checkAllAutre">Tout sélectionner</label>
                    </div>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 search-table" id="tableAutre">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;"><i class="fas fa-check-square"></i></th>
                            <th>Référence</th>
                            <th>Horodatage</th>
                            <th>Destinataire</th>
                            <th>Mode & Transporteur</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandesAutre as $c)
                        <tr class="align-middle">
                            <td class="ps-4 text-center">
                                <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input fs-5 checkbox-Autre" style="cursor: pointer;">
                            </td>
                            <td>
                                <span class="fw-bold text-dark">#{{ $c->id_commande }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m/Y') }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($c->date_commande)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials">{{ substr($c->utilisateur->prenom, 0, 1) }}{{ substr($c->utilisateur->nom, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</div>
                                        <div class="text-muted small searchable-text"><i class="fas fa-map-marker-alt text-secondary me-1"></i>{{ $c->adresse->ville_adresse ?? 'Ville inconnue' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($c->type_livraison == 'Express')
                                    <span class="status-badge bg-danger text-white"><i class="fas fa-shipping-fast me-1"></i> EXPRESS</span>
                                @elseif($c->type_livraison == 'Relais')
                                    <span class="status-badge bg-info text-dark"><i class="fas fa-store me-1"></i> RELAIS</span>
                                @else
                                    <span class="status-badge bg-secondary text-white">{{ strtoupper($c->type_livraison) }}</span>
                                @endif
                                <div class="small text-muted mt-1 ms-1">DHL / Chronopost</div>
                            </td>
                            <td class="text-end pe-4">
                                @if(!empty($c->utilisateur->telephone))
                                    <button type="button" onclick="document.getElementById('sms-form-{{ $c->id_commande }}').submit();" class="btn btn-sm btn-outline-primary fw-bold border-0 bg-light text-primary" title="Envoyer confirmation SMS">
                                        <i class="fas fa-comment-dots me-1"></i> Notifier
                                    </button>
                                @else
                                    <span class="d-inline-flex align-items-center justify-content-center text-danger bg-danger bg-opacity-10 rounded-circle" style="width: 32px; height: 32px;" title="Numéro de téléphone manquant - Action impossible">
                                        <i class="fas fa-phone-slash"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="60" class="opacity-25 mb-3" alt="Empty">
                                <p class="text-muted fw-bold">Aucune commande prioritaire en attente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BLOC 2 : STANDARD --}}
        <div class="card table-card shadow-sm mb-5">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-primary mb-0 d-flex align-items-center">
                        <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.9rem;"><i class="fas fa-home"></i></span>
                        File Standard (Domicile)
                    </h5>
                    @if($commandesDomicile->count() > 0)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAllDomicile" onclick="toggleAll('Domicile')">
                        <label class="form-check-label text-muted small fw-bold" for="checkAllDomicile">Tout sélectionner</label>
                    </div>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 search-table" id="tableDomicile">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;"><i class="fas fa-check-square"></i></th>
                            <th>Référence</th>
                            <th>Horodatage</th>
                            <th>Destinataire</th>
                            <th>Transporteur</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandesDomicile as $c)
                        <tr class="align-middle">
                            <td class="ps-4 text-center">
                                <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input fs-5 checkbox-Domicile" style="cursor: pointer;">
                            </td>
                            <td>
                                <span class="fw-bold text-secondary">#{{ $c->id_commande }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m/Y') }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($c->date_commande)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials">{{ substr($c->utilisateur->prenom, 0, 1) }}{{ substr($c->utilisateur->nom, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</div>
                                        <div class="text-muted small searchable-text"><i class="fas fa-map-marker-alt text-secondary me-1"></i>{{ $c->adresse->ville_adresse ?? 'Ville inconnue' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge bg-light text-dark border border-light-subtle">
                                    <i class="fas fa-truck me-1 text-muted"></i> La Poste / Colissimo
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if(!empty($c->utilisateur->telephone))
                                    <button type="button" onclick="document.getElementById('sms-form-{{ $c->id_commande }}').submit();" class="btn btn-sm btn-light text-muted border-0" title="Envoyer SMS">
                                        <i class="far fa-comment-alt"></i>
                                    </button>
                                @else
                                    <span class="text-muted opacity-25" title="Pas de numéro"><i class="fas fa-phone-slash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted fw-bold">Aucune commande standard en attente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. ACTION BAR FLOTTANTE (Sticky Footer) --}}
        <div class="floating-action-bar fixed-bottom py-3 px-4">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-dolly"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block">Prêt pour le chargement ?</span>
                        <span class="text-muted small">Cochez les commandes remises au chauffeur.</span>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-light border fw-bold text-muted">
                        <i class="fas fa-print me-2"></i>Imprimer bordereau
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="fas fa-check-double me-2"></i> Valider l'expédition
                    </button>
                </div>
            </div>
        </div>

    </form>

    {{-- Formulaires cachés (SMS) --}}
    @foreach($commandesDomicile->merge($commandesAutre) as $c)
        @if(!empty($c->utilisateur->telephone))
            <form id="sms-form-{{ $c->id_commande }}" action="{{ route('service.expedition.sms', $c->id_commande) }}" method="POST" class="d-none">@csrf</form>
        @endif
    @endforeach

    {{-- Spacer pour ne pas cacher le bas de page avec la barre flottante --}}
    <div style="height: 100px;"></div>

</div>

<script>
    // 1. Script de selection de masse
    function toggleAll(type) {
        const master = document.getElementById('checkAll' + type);
        const checkboxes = document.getElementsByClassName('checkbox-' + type);
        for (let i = 0; i < checkboxes.length; i++) { checkboxes[i].checked = master.checked; }
    }

    // 2. Recherche Instantanée
    document.getElementById('globalSearch').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('.search-table tbody tr');
        
        rows.forEach(row => {
            // On cherche uniquement dans les éléments marqués 'searchable-text' ou les IDs
            // Si pas de résultat, on cache.
            if(row.innerText.toLowerCase().indexOf(value) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
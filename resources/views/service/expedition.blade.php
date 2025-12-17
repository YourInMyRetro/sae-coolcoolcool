@extends('layout')

@section('content')
<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    
    /* Styles pour les Cartes et Badges */
    .kpi-card { transition: transform 0.2s; border: none; border-radius: 12px; }
    .kpi-card:hover { transform: translateY(-3px); }
    .table-card { border-radius: 12px; border: none; overflow: hidden; }
    .status-badge { padding: 0.35em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
    
    /* Tableaux */
    .table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; border-bottom: 2px solid #eaecf0; padding: 1rem; }
    .table tbody td { padding: 1rem; vertical-align: middle; color: #344767; font-size: 0.9rem; }
    .avatar-initials { width: 35px; height: 35px; background: #e9ecef; color: #495057; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 0.8rem; margin-right: 10px; }
    
    /* Barre de recherche et Footer */
    .search-bar { border-radius: 50px; border: 1px solid #dee2e6; padding-left: 1.5rem; height: 50px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
    .floating-action-bar { background: white; border-top: 1px solid #dee2e6; box-shadow: 0 -4px 20px rgba(0,0,0,0.05); z-index: 1000; }
</style>

<div class="container py-5">

    {{-- HEADER --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-5">
            <h1 class="h3 fw-bold text-dark mb-1">Pilotage Expéditions</h1>
            <p class="text-muted mb-0">Gestion des départs du {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-7">
            <div class="position-relative">
                <input type="text" id="globalSearch" class="form-control search-bar" placeholder="Rechercher (Client, Ville, Réf)...">
                <i class="fas fa-search text-muted position-absolute" style="right: 20px; top: 17px;"></i>
            </div>
        </div>
    </div>

    {{-- KPIS --}}
    <div class="row g-4 mb-5">
        {{-- KPI EXPRESS --}}
        <div class="col-md-6">
            <div class="card kpi-card shadow-sm h-100" style="background: linear-gradient(135deg, #fff 0%, #fff5f5 100%); border-left: 6px solid #dc3545;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-danger fw-bold small mb-2"><i class="fas fa-stopwatch me-1"></i> Prioritaire / Express</div>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 fw-bold text-dark me-2">{{ count($commandesAutre) }}</span>
                            <span class="text-muted">colis</span>
                        </div>
                        <div class="mt-2 badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                            <i class="fas fa-clock me-1"></i> Deadline : {{ str_replace('Demain ', '', $creneauAutre) }}
                        </div>
                    </div>
                    <div class="bg-white p-3 rounded-circle shadow-sm text-danger"><i class="fas fa-shipping-fast fa-2x"></i></div>
                </div>
            </div>
        </div>

        {{-- KPI STANDARD --}}
        <div class="col-md-6">
            <div class="card kpi-card shadow-sm h-100" style="background: linear-gradient(135deg, #fff 0%, #f0f7ff 100%); border-left: 6px solid #0d6efd;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase text-primary fw-bold small mb-2"><i class="fas fa-box me-1"></i> Standard / Domicile</div>
                        <div class="d-flex align-items-baseline">
                            <span class="display-4 fw-bold text-dark me-2">{{ count($commandesDomicile) }}</span>
                            <span class="text-muted">colis</span>
                        </div>
                        <div class="mt-2 badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="fas fa-truck-loading me-1"></i> Enlèvement : {{ $creneauDomicile }}
                        </div>
                    </div>
                    <div class="bg-white p-3 rounded-circle shadow-sm text-primary"><i class="fas fa-home fa-2x"></i></div>
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
    
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
            <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    {{-- 
        =======================================================
        LA FENÊTRE POP-UP (MODALE)
        PLACÉE ICI (ENTRE LES MESSAGES ET LA LISTE)
        =======================================================
    --}}
    <div class="modal fade" id="globalSmsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="globalSmsForm" action="" method="POST">
                    @csrf
                    <div class="modal-header bg-light border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-comment-dots text-primary me-2"></i>Nouveau Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded border">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" id="modalClientName">Chargement...</div>
                                <div class="small text-muted" id="modalClientPhone">...</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-uppercase text-muted fw-bold">Message à envoyer</label>
                            <textarea name="message_sms" class="form-control bg-white" rows="4" style="resize: none;" required>Bonjour, votre commande est prête et a été remise au transporteur ce jour. Elle sera livrée très prochainement.</textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-dark fw-bold px-4">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FORMULAIRE D'EXPÉDITION GLOBALE (ID 27) --}}
    <form action="{{ route('service.expedition.pickup') }}" method="POST">
        @csrf

        {{-- TABLEAU 1 : EXPRESS --}}
        <div class="card table-card shadow-sm mb-5">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-danger mb-0"><i class="fas fa-bolt me-2"></i>File Prioritaire</h5>
                @if($commandesAutre->count() > 0)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="checkAllAutre" onclick="toggleAll('Autre')">
                    <label class="form-check-label text-muted small fw-bold" for="checkAllAutre">Tout cocher</label>
                </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 search-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;"><i class="fas fa-check-square"></i></th>
                            <th>Réf</th>
                            <th>Date</th>
                            <th>Destinataire</th>
                            <th>Mode</th>
                            <th class="text-end pe-4">SMS (ID 28)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandesAutre as $c)
                        <tr class="align-middle">
                            <td class="ps-4 text-center">
                                <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input fs-5 checkbox-Autre" style="cursor: pointer;">
                            </td>
                            <td><span class="fw-bold text-dark">#{{ $c->id_commande }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials">{{ substr($c->utilisateur->prenom, 0, 1) }}{{ substr($c->utilisateur->nom, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</div>
                                        <div class="text-muted small searchable-text">{{ optional($c->adresse)->ville_adresse ?? 'Inconnue' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($c->type_livraison == 'Express') <span class="status-badge bg-danger text-white">EXPRESS</span>
                                @elseif($c->type_livraison == 'Relais') <span class="status-badge bg-info text-dark">RELAIS</span>
                                @else <span class="status-badge bg-secondary text-white">{{ strtoupper($c->type_livraison) }}</span> @endif
                            </td>
                            <td class="text-end pe-4">
                                @if(!empty($c->utilisateur->telephone))
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold border-0 bg-light text-dark" 
                                            onclick="openSmsModal(this)"
                                            data-id="{{ $c->id_commande }}"
                                            data-nom="{{ $c->utilisateur->prenom }} {{ $c->utilisateur->nom }}"
                                            data-tel="{{ $c->utilisateur->telephone }}"
                                            title="Envoyer SMS">
                                        <i class="fas fa-pen me-1"></i> Saisir
                                    </button>
                                @else
                                    <span class="text-muted small opacity-50"><i class="fas fa-phone-slash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Aucune commande prioritaire.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABLEAU 2 : STANDARD --}}
        <div class="card table-card shadow-sm mb-5">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0"><i class="fas fa-home me-2"></i>File Standard</h5>
                @if($commandesDomicile->count() > 0)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="checkAllDomicile" onclick="toggleAll('Domicile')">
                    <label class="form-check-label text-muted small fw-bold" for="checkAllDomicile">Tout cocher</label>
                </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 search-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;"><i class="fas fa-check-square"></i></th>
                            <th>Réf</th>
                            <th>Date</th>
                            <th>Destinataire</th>
                            <th>Mode</th>
                            <th class="text-end pe-4">SMS (ID 28)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandesDomicile as $c)
                        <tr class="align-middle">
                            <td class="ps-4 text-center">
                                <input type="checkbox" name="commandes[]" value="{{ $c->id_commande }}" class="form-check-input fs-5 checkbox-Domicile" style="cursor: pointer;">
                            </td>
                            <td><span class="fw-bold text-secondary">#{{ $c->id_commande }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($c->date_commande)->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials">{{ substr($c->utilisateur->prenom, 0, 1) }}{{ substr($c->utilisateur->nom, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark searchable-text">{{ $c->utilisateur->nom }} {{ $c->utilisateur->prenom }}</div>
                                        <div class="text-muted small searchable-text">{{ optional($c->adresse)->ville_adresse ?? 'Inconnue' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge bg-light text-dark border">STANDARD</span></td>
                            <td class="text-end pe-4">
                                @if(!empty($c->utilisateur->telephone))
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold border-0 bg-light text-dark" 
                                            onclick="openSmsModal(this)"
                                            data-id="{{ $c->id_commande }}"
                                            data-nom="{{ $c->utilisateur->prenom }} {{ $c->utilisateur->nom }}"
                                            data-tel="{{ $c->utilisateur->telephone }}"
                                            title="Envoyer SMS">
                                        <i class="fas fa-pen me-1"></i> Saisir
                                    </button>
                                @else
                                    <span class="text-muted small opacity-50"><i class="fas fa-phone-slash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Aucune commande standard.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ACTION BAR (Sticky Footer) --}}
        <div class="floating-action-bar fixed-bottom py-3 px-4">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-dolly"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block">Chargement Transporteur</span>
                        <span class="text-muted small">Cochez les commandes remises au chauffeur.</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm text-uppercase">
                    <i class="fas fa-check-double me-2"></i> Confirmer Départ
                </button>
            </div>
        </div>
    </form>

    <div style="height: 100px;"></div>

</div>

<script>
    // 1. Fonction pour ouvrir la modale et remplir les infos
    function openSmsModal(button) {
        let id = button.getAttribute('data-id');
        let nom = button.getAttribute('data-nom');
        let tel = button.getAttribute('data-tel');

        document.getElementById('modalClientName').innerText = nom;
        document.getElementById('modalClientPhone').innerText = tel;

        let form = document.getElementById('globalSmsForm');
        let baseUrl = "{{ route('service.expedition.sms', '000') }}"; 
        form.action = baseUrl.replace('000', id);

        var myModal = new bootstrap.Modal(document.getElementById('globalSmsModal'));
        myModal.show();
    }

    // 2. Cocher tout
    function toggleAll(type) {
        const master = document.getElementById('checkAll' + type);
        const checkboxes = document.getElementsByClassName('checkbox-' + type);
        for (let i = 0; i < checkboxes.length; i++) { checkboxes[i].checked = master.checked; }
    }

    // 3. Recherche
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
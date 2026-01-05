@extends('layout')

@section('content')
<div class="container" style="padding: 20px; max-width: 1000px;">
    <h1 style="color: #326295; margin-bottom: 30px;">Espace Service Commande</h1>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; padding: 15px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ID 42 & 43 : Gestion des Réceptions (Acceptation / Refus) --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 40px; border-left: 5px solid #2ecc71;">
        <h3 style="color: #27ae60;"><i class="fas fa-check-double"></i> Validation des Réceptions Client</h3>
        <p>Commandes actuellement "Expédiées". Le client a-t-il accepté ou refusé le colis ?</p>
        
        <table class="table table-bordered" style="width: 100%; margin-top: 15px;">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client ID</th>
                    <th>Date Commande</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commandesEnCours as $cmd)
                <tr>
                    <td>#{{ $cmd->id_commande }} ({{ $cmd->type_livraison }})</td>
                    <td>{{ $cmd->id_utilisateur }}</td>
                    <td>{{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y') }}</td>
                    <td>
                        {{-- ID 42 : Accepter la livraison --}}
                        <form action="{{ route('service.commande.valider', $cmd->id_commande) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-success" style="background-color: #2ecc71; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius:4px;">
                                <i class="fas fa-check"></i> Accepter (Livré)
                            </button>
                        </form>

                        {{-- ID 43 : Refuser (Saisir un incident/réserve) --}}
                        {{-- Un clic ici remplit le formulaire plus bas pour gagner du temps --}}
                        <button type="button" onclick="prefillReserve({{ $cmd->id_commande }})" class="btn btn-warning" style="background-color: #f39c12; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius:4px;">
                            <i class="fas fa-times"></i> Refuser (Réserve)
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; color: #7f8c8d;">Aucune commande en attente de réception client.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tableau Qualité (Commandes Express) --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 40px;">
        <h3><i class="fas fa-tachometer-alt"></i> Contrôle Qualité - Livraisons Express</h3>
        <table class="table table-bordered" style="width: 100%; margin-top: 15px;">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Date Commande</th>
                    <th>Date Livraison Réelle</th>
                    <th>Délai constaté</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandesExpress as $cmd)
                <tr>
                    <td>#{{ $cmd->id_commande }}</td>
                    <td>{{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($cmd->suivi && $cmd->suivi->date_statut_final)
                            {{ \Carbon\Carbon::parse($cmd->suivi->date_statut_final)->format('d/m/Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($cmd->suivi && $cmd->suivi->date_statut_final)
                            {{ \Carbon\Carbon::parse($cmd->date_commande)->diffInDays(\Carbon\Carbon::parse($cmd->suivi->date_statut_final)) }} jours
                        @else
                            En cours
                        @endif
                    </td>
                    <td>
                        {{ $cmd->statut_livraison }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Saisie de Réserve (ID 43 suite) --}}
    <div id="bloc-reserve" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="color: #c0392b;"><i class="fas fa-exclamation-triangle"></i> Saisir une réserve client (Refus ou Incident)</h3>
        <p>Sélectionnez une commande pour signaler un problème (refus de livraison, colis abîmé, manquant...).</p>
        
        <form action="{{ route('service.reserve.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Commande concernée :</label>
                <select name="commande_id" id="reserve_commande_id" class="form-control" style="width: 100%; padding: 10px;">
                    <optgroup label="En cours de livraison (Refus immédiat)">
                        @foreach($commandesEnCours as $cmd)
                            <option value="{{ $cmd->id_commande }}">
                                Commande #{{ $cmd->id_commande }} - En cours ({{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Déjà Livré (SAV après coup)">
                        @foreach($commandesLivre as $cmd)
                            <option value="{{ $cmd->id_commande }}">
                                Commande #{{ $cmd->id_commande }} - Livrée ({{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Motif de la réserve :</label>
                <textarea name="motif" class="form-control" rows="3" style="width: 100%; padding: 10px;" placeholder="Ex: Client refuse le colis car endommagé..."></textarea>
            </div>

            <button type="submit" class="btn btn-danger" style="background-color: #c0392b; color: white; border: none; padding: 10px 20px; cursor: pointer;">
                Enregistrer la réserve / Le refus
            </button>
        </form>
    </div>
</div>

<script>
    function prefillReserve(id) {
        let select = document.getElementById('reserve_commande_id');
        select.value = id;
        
        document.getElementById('bloc-reserve').scrollIntoView({behavior: "smooth"});
        
        alert("Veuillez saisir le motif du refus pour la commande #" + id + " ci-dessous.");
    }
</script>
@endsection
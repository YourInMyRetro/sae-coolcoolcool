@extends('layout')

@section('content')
<div class="container" style="padding: 20px; max-width: 1000px;">
    <h1 style="color: #326295; margin-bottom: 30px;">Espace Service Commande</h1>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; padding: 15px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- SPRINT 2 : Tableau Qualité (Commandes Express) --}}
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

    {{-- SPRINT 3 : Saisie de Réserve --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="color: #c0392b;"><i class="fas fa-exclamation-triangle"></i> Saisir une réserve client</h3>
        <p>Sélectionnez une commande livrée pour signaler un problème (colis abîmé, manquant...).</p>
        
        <form action="{{ route('service.reserve.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Commande concernée :</label>
                <select name="commande_id" class="form-control" style="width: 100%; padding: 10px;">
                    @foreach($commandesLivre as $cmd)
                        <option value="{{ $cmd->id_commande }}">
                            Commande #{{ $cmd->id_commande }} - {{ number_format($cmd->montant_total, 2) }} € ({{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Motif de la réserve :</label>
                <textarea name="motif" class="form-control" rows="3" style="width: 100%; padding: 10px;" placeholder="Ex: Carton reçu ouvert, produit manquant..."></textarea>
            </div>

            <button type="submit" class="btn btn-danger" style="background-color: #c0392b; color: white; border: none; padding: 10px 20px; cursor: pointer;">
                Enregistrer la réserve
            </button>
        </form>
    </div>
</div>
@endsection
@extends('layout')

@section('content')
<div class="container mt-5">
    <h2>Dashboard Expédition (Commandes Express)</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('service.expedition.pickup') }}" method="POST">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Commandes à remettre au transporteur (Prochain départ)</h5>
            </div>
            <div class="card-body">
                @if($commandesAExpedier->isEmpty())
                    <p>Aucune commande Express en attente.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sélection</th>
                                <th>Date Commande</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandesAExpedier as $commande)
                            <tr>
                                <td>
                                    <input type="checkbox" name="commandes[]" value="{{ $commande->id_commande }}">
                                </td>
                                <td>{{ $commande->date_commande }}</td>
                                <td>
                                    {{ $commande->utilisateur->nom }} {{ $commande->utilisateur->prenom }}<br>
                                    <small>{{ $commande->utilisateur->mail }}</small>
                                </td>
                                <td>{{ $commande->montant_total }} €</td>
                                <td>
                                    <button type="submit" form="form-sms-{{ $commande->id_commande }}" class="btn btn-sm btn-info">
                                        📱 Envoyer SMS
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <button type="submit" class="btn btn-primary mt-3">
                        🚚 Valider la prise en charge transporteur
                    </button>
                @endif
            </div>
        </div>
    </form>

    {{-- Formulaires cachés pour les SMS individuels --}}
    @foreach($commandesAExpedier as $commande)
        <form id="form-sms-{{ $commande->id_commande }}" 
              action="{{ route('service.expedition.sms', $commande->id_commande) }}" 
              method="POST" style="display: none;">
            @csrf
        </form>
    @endforeach
</div>
@endsection
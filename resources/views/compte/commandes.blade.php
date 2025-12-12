@extends('layout')

@section('content')
<div class="container" style="padding: 40px;">
    <h2 class="section-title">Historique de mes commandes</h2>

    @if($commandes->isEmpty())
        <p>Vous n'avez pas encore passé de commande.</p>
    @else
        <table class="table table-striped" style="width: 100%; margin-top: 20px;">
            <thead style="background-color: #326295; color: white;">
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Type</th>
                    <th>État</th>
                    <th>Livraison</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandes as $cmd)
                <tr>
                    <td>#{{ $cmd->id_commande }}</td>
                    {{-- On formate la date SQL --}}
                    <td>{{ \Carbon\Carbon::parse($cmd->date_commande)->format('d/m/Y') }}</td>
                    <td>{{ number_format($cmd->montant_total, 2) }} €</td>
                    <td>{{ $cmd->type_livraison }}</td>
                    <td>
                        @if($cmd->statut_livraison == 'Réserve')
                            <span style="color: red; font-weight: bold;">Litige / Réserve</span>
                        @elseif($cmd->statut_livraison == 'Livrée')
                            <span style="color: green; font-weight: bold;">Livrée</span>
                        @else
                            {{ $cmd->statut_livraison }}
                        @endif
                    </td>
                    <td>
                        {{-- On vérifie si le suivi existe grâce à ton modèle existant --}}
                        @if($cmd->suivi && $cmd->suivi->date_statut_final)
                            Livré le : {{ \Carbon\Carbon::parse($cmd->suivi->date_statut_final)->format('d/m/Y') }}
                        @else
                            En cours d'acheminement
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
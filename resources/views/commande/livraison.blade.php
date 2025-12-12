@extends('layout')

@section('content')
<div class="container" style="max-width: 800px; padding: 40px 20px;">
    <h2 class="section-title">Mode de Livraison</h2>

    <form action="{{ route('commande.validerLivraison') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        @csrf
        
        {{-- Choix Adresse Existante --}}
        @if($adresses->count() > 0)
            <h4 style="color: #326295; margin-bottom: 15px;">Vos adresses enregistrées :</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                @foreach($adresses as $adr)
                    <label style="border: 2px solid #eee; padding: 15px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onclick="document.querySelectorAll('label').forEach(l => l.style.borderColor='#eee'); this.style.borderColor='#326295';">
                        <input type="radio" name="id_adresse_existante" value="{{ $adr->id_adresse }}">
                        <strong>{{ $adr->rue }}</strong><br>
                        {{ $adr->code_postal_adresse }} {{ $adr->ville_adresse }}
                    </label>
                @endforeach
            </div>
            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
        @endif

        {{-- Formulaire Nouvelle Adresse --}}
        <h4 style="color: #326295; margin-bottom: 15px;">Ou nouvelle adresse :</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; position: relative;">
            {{-- Ajout de l'ID 'search-adresse' et 'autocomplete="off"' --}}
            <div style="grid-column: span 2; position: relative;">
                <input type="text" id="search-adresse" name="rue" placeholder="Commencez à taper votre adresse..." class="form-control" autocomplete="off" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                {{-- Liste des suggestions --}}
                <ul id="suggestions-liste" style="list-style: none; padding: 0; margin: 0; position: absolute; width: 100%; background: white; border: 1px solid #ddd; border-top: none; z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></ul>
            </div>

            {{-- Ajout des IDs pour le remplissage automatique --}}
            <input type="text" id="code_postal" name="code_postal_adresse" placeholder="Code Postal" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" id="ville" name="ville_adresse" placeholder="Ville" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" name="pays_adresse" value="France" placeholder="Pays" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <button type="submit" style="width: 100%; padding: 15px; background-color: #326295; color: white; border: none; font-weight: bold; font-size: 1.1rem; border-radius: 5px; cursor: pointer; margin-top: 10px;">
            Continuer vers le paiement <i class="fas fa-credit-card"></i>
        </button>
    </form>
</div>

{{-- Script pour l'API Adresse --}}
<script>
    const inputAdresse = document.getElementById('search-adresse');
    const suggestionsListe = document.getElementById('suggestions-liste');
    const inputCP = document.getElementById('code_postal');
    const inputVille = document.getElementById('ville');

    inputAdresse.addEventListener('input', function() {
        const query = this.value;
        if (query.length > 3) {
            // Appel à l'API Data.gouv.fr
            fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    suggestionsListe.innerHTML = '';
                    suggestionsListe.style.display = 'block';
                    
                    data.features.forEach(feature => {
                        const li = document.createElement('li');
                        li.style.padding = '10px';
                        li.style.cursor = 'pointer';
                        li.style.borderBottom = '1px solid #eee';
                        li.innerText = feature.properties.label;
                        
                        // Au clic sur une suggestion
                        li.addEventListener('click', function() {
                            inputAdresse.value = feature.properties.name; // Juste le nom de la rue
                            inputCP.value = feature.properties.postcode;
                            inputVille.value = feature.properties.city;
                            suggestionsListe.style.display = 'none';
                        });

                        li.addEventListener('mouseover', () => li.style.backgroundColor = '#f0f0f0');
                        li.addEventListener('mouseout', () => li.style.backgroundColor = 'white');
                        
                        suggestionsListe.appendChild(li);
                    });
                });
        } else {
            suggestionsListe.style.display = 'none';
        }
    });

    // Cacher la liste si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (e.target !== inputAdresse) {
            suggestionsListe.style.display = 'none';
        }
    });
</script>
@endsection
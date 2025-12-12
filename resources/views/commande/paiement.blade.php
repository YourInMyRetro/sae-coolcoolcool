@extends('layout')

@section('content')
{{-- Inclusion de Cleave.js --}}
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>

<div class="container" style="max-width: 600px; padding: 40px 20px;">
    <h2 class="section-title">Paiement Sécurisé</h2>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
        <span style="font-size: 1.2rem; color: #666;">Montant à payer :</span>
        <div style="font-size: 2.5rem; font-weight: 800; color: #326295;">{{ number_format($total + 5, 2) }} €</div>
        <small style="color: #999;">(dont 5.00 € de frais de port)</small>
    </div>

    <form action="{{ route('commande.processPaiement') }}" method="POST" id="payment-form">
        @csrf
        <input type="hidden" name="total_amount" value="{{ $total }}">

        @if(count($cartes) > 0)
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                <div style="padding: 10px 15px; background: #eee; font-weight: bold;">Mes cartes</div>
                @foreach($cartes as $carte)
                    <label style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;">
                        <input type="radio" name="use_saved_card" value="{{ $carte->id_cb }}" style="margin-right: 15px;">
                        <span style="flex-grow: 1;">Carte **** **** **** {{ substr($carte->numero_chiffre, -4) }}</span>
                        <i class="fab fa-cc-visa" style="font-size: 1.5rem; color: #326295;"></i>
                    </label>
                @endforeach
                <label style="display: flex; align-items: center; padding: 15px; cursor: pointer;">
                    <input type="radio" name="use_saved_card" value="" checked style="margin-right: 15px;">
                    <span>Utiliser une autre carte</span>
                </label>
            </div>
        @endif

        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label>Numéro de carte</label>
                <div style="position: relative;">
                    <input type="text" id="input-card" name="card_number" placeholder="0000 0000 0000 0000" style="width: 100%; padding: 12px; padding-left: 40px; border: 1px solid #ccc; border-radius: 4px;">
                    <i class="fas fa-credit-card" style="position: absolute; left: 12px; top: 14px; color: #999;"></i>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label>Expiration</label>
                    <input type="text" id="input-expiry" name="expiration" placeholder="MM/YY" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                    <div id="error-expiry" style="color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                </div>
                <div style="flex: 1;">
                    <label>CVC / CVV</label>
                    {{-- AJOUT : maxlength="3" force le navigateur à bloquer après 3 caractères --}}
                    {{-- AJOUT : inputmode="numeric" affiche le clavier numérique sur mobile --}}
                    <input type="text" id="input-cvc" name="ccv" placeholder="123" maxlength="3" inputmode="numeric" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>

            <div style="display: flex; align-items: center; margin-top: 15px;">
                <input type="checkbox" id="save_card" name="save_card" value="1" style="width: 18px; height: 18px; margin-right: 10px;">
                <label for="save_card" style="font-size: 0.9rem; color: #555;">Sauvegarder cette carte pour mes prochains achats</label>
            </div>
        </div>

        <button type="submit" id="btn-submit" style="width: 100%; padding: 18px; background-color: #27ae60; color: white; border: none; font-weight: bold; font-size: 1.2rem; border-radius: 5px; cursor: pointer; transition: background 0.3s;">
            Payer et terminer <i class="fas fa-check"></i>
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // 1. CONFIGURATION CLEAVE.JS
        // ---------------------------

        new Cleave('#input-card', {
            creditCard: true
        });

        new Cleave('#input-expiry', {
            date: true,
            datePattern: ['m', 'y']
        });

        // CORRECTION CVC : On utilise 'blocks' et 'numericOnly' au lieu de 'numeral'
        new Cleave('#input-cvc', {
            blocks: [3],       // Un seul bloc de 3 caractères max
            numericOnly: true  // Interdit strictement les lettres
        });


        // 2. LOGIQUES DE VALIDATION
        // -------------------------
        const form = document.getElementById('payment-form');
        const expiryInput = document.getElementById('input-expiry');
        const errorExpiry = document.getElementById('error-expiry');
        const cvcInput = document.getElementById('input-cvc');

        // Validation Date (Identique à avant car validé par tes tests)
        function validateDate() {
            const value = expiryInput.value;
            if (value.length < 5) {
                errorExpiry.style.display = 'none';
                return false;
            }
            const parts = value.split('/');
            const month = parseInt(parts[0], 10);
            const year = parseInt(parts[1], 10);
            const now = new Date();
            const currentYearTwoDigits = parseInt(now.getFullYear().toString().substr(-2));
            const currentMonth = now.getMonth() + 1;

            let errorMessage = '';

            if (year < currentYearTwoDigits || (year === currentYearTwoDigits && month < currentMonth)) {
                errorMessage = "Votre carte semble expirée.";
            } else if (year > 40) { 
                errorMessage = "Année d'expiration invalide (max 2040).";
            } else if (month < 1 || month > 12) {
                errorMessage = "Mois invalide.";
            }

            if (errorMessage) {
                errorExpiry.textContent = errorMessage;
                errorExpiry.style.display = 'block';
                expiryInput.style.borderColor = '#e74c3c';
                return false;
            } else {
                errorExpiry.style.display = 'none';
                expiryInput.style.borderColor = '#ccc';
                return true;
            }
        }

        // Validation CVC Visuelle (Rouge si pas 3 chiffres)
        function validateCVC() {
            // On vérifie que c'est bien 3 chiffres exactement
            if (cvcInput.value.length !== 3) {
                cvcInput.style.borderColor = '#e74c3c';
                return false;
            } else {
                cvcInput.style.borderColor = '#ccc';
                return true;
            }
        }

        // Écouteurs d'événements
        expiryInput.addEventListener('blur', validateDate);
        expiryInput.addEventListener('input', () => {
             errorExpiry.style.display = 'none';
             expiryInput.style.borderColor = '#ccc';
        });

        cvcInput.addEventListener('input', () => {
             // Si l'utilisateur tape, on remet la couleur normale
             // Le blocage > 3 est géré par Cleave + maxlength
             if(cvcInput.value.length <= 3) cvcInput.style.borderColor = '#ccc';
        });

        // 3. BLOCAGE DE L'ENVOI
        // ---------------------
        form.addEventListener('submit', function(e) {
            const savedCardRadio = document.querySelector('input[name="use_saved_card"]:checked');
            // Si carte enregistrée choisie, on passe
            if (savedCardRadio && savedCardRadio.value !== "") {
                return;
            }

            // Vérifications strictes
            const isDateValid = validateDate();
            const isCvcValid = validateCVC();
            const cardVal = document.getElementById('input-card').value.replace(/\s/g, ''); // On enlève les espaces pour compter

            let errors = [];

            if (cardVal.length < 13) errors.push("Numéro de carte incomplet");
            if (!isDateValid) errors.push("Date invalide");
            if (!isCvcValid) errors.push("Le CVC doit comporter exactement 3 chiffres");

            if (errors.length > 0) {
                e.preventDefault(); // BLOQUE LE FORMULAIRE
                alert("Attention :\n- " + errors.join("\n- "));
            }
        });
    });
</script>
@endsection
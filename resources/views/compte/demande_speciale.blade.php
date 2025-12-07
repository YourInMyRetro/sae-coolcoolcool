@extends('layout')

@section('content')
<div class="account-page-wrapper">
    <div class="account-container" style="max-width: 800px;">
        
        <div class="account-header">
            <div>
                <h1 style="color: #00d4ff;">Demande de Conception <span class="badge-pro-title">B2B</span></h1>
                <p style="color: #888;">Soumettez votre projet au bureau d'étude FIFA.</p>
            </div>
            <a href="{{ route('compte.index') }}" class="btn-cancel-dark">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="account-card" style="border-top: 4px solid #00d4ff;">
            
            <form action="{{ route('compte.demande.store') }}" method="POST">
                @csrf
                
                <div class="pro-edit-section" style="margin-top: 0;">
                    <h3><i class="fas fa-building"></i> Demandeur</h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Société</label>
                            <input type="text" value="{{ $user->professionel->nom_societe }}" class="fifa-input" disabled>
                        </div>
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Contact Principal</label>
                            <input type="text" value="{{ $user->prenom }} {{ $user->nom }}" class="fifa-input" disabled>
                        </div>
                    </div>
                    <div class="fifa-form-group">
                        <label>Email de contact</label>
                        <input type="text" value="{{ $user->mail }}" class="fifa-input" disabled>
                    </div>
                </div>

                <div class="fifa-form-group">
                    <label>Sujet de la demande *</label>
                    <input type="text" name="sujet" class="fifa-input" placeholder="Ex: Devis pour 50 maillots personnalisés..." required>
                </div>

                <div class="fifa-form-group">
                    <label>Téléphone de contact *</label>
                    <input type="text" name="telephone" class="fifa-input" placeholder="+33 6 ..." required>
                </div>

                <div class="fifa-form-group">
                    <label>Description du besoin (Détails, Quantités, Délais...) *</label>
                    <textarea name="description_besoin" class="fifa-input" rows="6" style="resize: vertical;" required placeholder="Décrivez votre projet en détail pour notre bureau d'étude..."></textarea>
                </div>

                <div style="margin-top: 30px; border-top: 1px solid #333; padding-top: 20px; text-align: right;">
                    <button type="submit" class="btn-save-changes" style="background: linear-gradient(135deg, #00d4ff 0%, #008eb3 100%); color: white;">
                        Envoyer la demande <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
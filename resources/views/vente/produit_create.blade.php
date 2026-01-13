@extends('layout')

@section('content')
<div class="forge-wrapper">
    <div class="container py-5">
        
        <form action="{{ route('vente.produit.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-end mb-5 fade-in-down">
                <div>
                    <div class="system-badge">SYSTEM: ONLINE</div>
                    <h1 class="forge-title">NOUVEAU PRODUIT</h1>
                    <p class="text-muted">Configuration de l'article pour la boutique</p>
                </div>
                <div class="actions">
                    <a href="{{ route('vente.dashboard') }}" class="btn-glass-back">
                        <i class="fas fa-times"></i> ANNULER
                    </a>
                    <button type="submit" class="btn-neon-save">
                        <i class="fas fa-save"></i> CRÉER LE PRODUIT
                    </button>
                </div>
            </div>

            <div class="row g-4">
                
                {{-- COLONNE GAUCHE : INFO GÉNÉRALES --}}
                <div class="col-lg-4">
                    <div class="glass-panel fade-in-up" style="animation-delay: 0.1s;">
                        <div class="panel-header">
                            <i class="fas fa-cube"></i> INFORMATIONS
                        </div>
                        <div class="panel-body">
                            
                            <div class="form-group mb-4">
                                <label>NOM DU PRODUIT</label>
                                <input type="text" name="nom_produit" class="cyber-input" placeholder="Ex: Maillot Domicile 2026" required>
                            </div>

                            <div class="form-group mb-4">
                                <label>CATÉGORIE</label>
                                <select name="id_categorie" class="cyber-input" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id_categorie }}">{{ $cat->nom_categorie }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label>DESCRIPTION</label>
                                <textarea name="description" class="cyber-input" rows="4" placeholder="Détails techniques..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>PHOTOS (MULTIPLES)</label>
                                <div class="upload-zone">
                                    <input type="file" name="photos[]" multiple class="file-input">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Glisser ou cliquer</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COLONNE DROITE : VARIANTES (MOTEUR) --}}
                <div class="col-lg-8">
                    <div class="glass-panel fade-in-up" style="animation-delay: 0.2s;">
                        <div class="panel-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-layer-group"></i> VARIANTES & STOCK</span>
                            <button type="button" class="btn-add-variant" onclick="addVariant()">
                                <i class="fas fa-plus"></i> AJOUTER COULEUR
                            </button>
                        </div>
                        
                        <div class="panel-body" id="variantsContainer">
                            {{-- LES VARIANTES S'AJOUTERONT ICI VIA JS --}}
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --bg-dark: #0b0e14;
        --panel-bg: rgba(20, 25, 35, 0.6);
        --border-color: rgba(255, 255, 255, 0.1);
        --neon-blue: #00f3ff;
        --neon-green: #00ff9d;
        --text-muted: #64748b;
    }

    .forge-wrapper {
        min-height: 100vh;
        background-color: var(--bg-dark);
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(0, 243, 255, 0.05) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(0, 255, 157, 0.05) 0%, transparent 40%);
        font-family: 'Rajdhani', sans-serif;
        color: white;
    }

    .system-badge {
        font-family: monospace; color: var(--neon-green); font-size: 0.8rem;
        margin-bottom: 5px; letter-spacing: 2px;
    }

    .forge-title {
        font-weight: 800; font-size: 2.5rem; margin: 0;
        text-shadow: 0 0 20px rgba(0, 243, 255, 0.3);
    }

    /* PANELS */
    .glass-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        overflow: hidden;
    }

    .panel-header {
        padding: 20px;
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid var(--border-color);
        font-weight: 700; letter-spacing: 1px; color: var(--neon-blue);
    }

    .panel-body { padding: 30px; }

    /* INPUTS */
    .form-group label {
        display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);
        margin-bottom: 8px; letter-spacing: 1px;
    }

    .cyber-input {
        width: 100%;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        color: white;
        padding: 12px 15px;
        border-radius: 6px;
        transition: 0.3s;
    }

    .cyber-input:focus {
        border-color: var(--neon-blue);
        box-shadow: 0 0 15px rgba(0, 243, 255, 0.1);
        outline: none;
    }

    /* UPLOAD ZONE */
    .upload-zone {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        position: relative;
        height: 100px;
        display: flex; align-items: center; justify-content: center;
        transition: 0.3s;
    }
    .upload-zone:hover { border-color: var(--neon-blue); background: rgba(0, 243, 255, 0.05); }
    .file-input { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .upload-content { text-align: center; color: var(--text-muted); pointer-events: none; }

    /* VARIANTS STYLING */
    .variant-row {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        animation: slideIn 0.5s ease;
    }

    .variant-header {
        display: flex; gap: 20px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px;
    }

    .size-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px;
    }

    .size-box {
        background: rgba(255,255,255,0.05);
        padding: 5px; border-radius: 4px; text-align: center;
    }
    .size-label { font-size: 0.7rem; color: var(--text-muted); display: block; }
    .size-input {
        width: 100%; background: transparent; border: none; color: white; text-align: center; font-weight: bold;
        border-bottom: 1px solid var(--border-color);
    }
    .size-input:focus { border-color: var(--neon-green); outline: none; }

    /* BUTTONS */
    .btn-neon-save {
        background: var(--neon-blue); color: #000; border: none;
        padding: 12px 30px; font-weight: 800; border-radius: 4px;
        transition: 0.3s;
        box-shadow: 0 0 20px rgba(0, 243, 255, 0.3);
    }
    .btn-neon-save:hover { transform: translateY(-2px); box-shadow: 0 0 30px rgba(0, 243, 255, 0.5); }

    .btn-glass-back {
        background: rgba(255,255,255,0.05); color: white; text-decoration: none;
        padding: 12px 20px; border-radius: 4px; margin-right: 10px; font-weight: 600;
        transition: 0.3s;
    }
    .btn-glass-back:hover { background: rgba(255,255,255,0.1); }

    .btn-add-variant {
        background: transparent; border: 1px solid var(--neon-green); color: var(--neon-green);
        padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;
        transition: 0.3s;
    }
    .btn-add-variant:hover { background: var(--neon-green); color: black; }

    .btn-remove-variant {
        position: absolute; top: 10px; right: 10px;
        background: transparent; border: none; color: #ff0055; cursor: pointer;
    }

    /* ANIMATIONS */
    .fade-in-down { animation: fadeDown 0.8s ease forwards; opacity: 0; }
    .fade-in-up { animation: fadeUp 0.8s ease forwards; opacity: 0; }
    @keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
</style>

<script>
    let variantIndex = 0;

    // Fonction pour ajouter une ligne de variante
    function addVariant() {
        const container = document.getElementById('variantsContainer');
        const div = document.createElement('div');
        div.className = 'variant-row';
        div.innerHTML = `
            <button type="button" class="btn-remove-variant" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
            
            <div class="variant-header">
                <div class="form-group flex-grow-1">
                    <label>COULEUR PRINCIPALE</label>
                    <input type="text" name="variantes[${variantIndex}][couleur]" class="cyber-input" placeholder="Ex: Bleu Roi" required list="colorsList">
                </div>
                <div class="form-group flex-grow-1">
                    <label>PRIX (€)</label>
                    <input type="number" name="variantes[${variantIndex}][prix]" class="cyber-input" placeholder="0.00" step="0.01" required>
                </div>
            </div>

            <div class="form-group">
                <label class="mb-2">STOCKS PAR TAILLE</label>
                <div class="size-grid">
                    <div class="size-box">
                        <span class="size-label">XS</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][XS]" class="size-input" placeholder="0">
                    </div>
                    <div class="size-box">
                        <span class="size-label">S</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][S]" class="size-input" placeholder="0">
                    </div>
                    <div class="size-box">
                        <span class="size-label">M</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][M]" class="size-input" placeholder="0">
                    </div>
                    <div class="size-box">
                        <span class="size-label">L</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][L]" class="size-input" placeholder="0">
                    </div>
                    <div class="size-box">
                        <span class="size-label">XL</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][XL]" class="size-input" placeholder="0">
                    </div>
                    <div class="size-box">
                        <span class="size-label">XXL</span>
                        <input type="number" name="variantes[${variantIndex}][tailles][XXL]" class="size-input" placeholder="0">
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        variantIndex++;
    }

    // Ajouter une variante au chargement pour ne pas avoir un écran vide
    document.addEventListener('DOMContentLoaded', () => {
        addVariant();
    });
</script>

{{-- Datalist pour l'autocomplétion des couleurs --}}
<datalist id="colorsList">
    @foreach($couleurs as $c)
        <option value="{{ $c->nom_couleur ?? $c->type_couleur }}">
    @endforeach
</datalist>
@endsection
@extends('layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Création de Campagne</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vente.votation.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nom_theme" class="form-label">Titre de la campagne</label>
                            <input type="text" class="form-control" name="nom_theme" id="nom_theme" placeholder="Ex: Joueur de l'année 2026" required>
                        </div>

                        <div class="mb-3">
                            <label for="date_fermeture" class="form-label">Date de clôture</label>
                            <input type="date" class="form-control" name="date_fermeture" id="date_fermeture" required>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('vente.votation.list') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i> Créer la campagne
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection@extends('layout')

@section('content')
<div class="stadium-wrapper">
    
    <div class="pitch-bg">
        <div class="grass-pattern"></div>
        <div class="center-circle"></div>
        <div class="half-way-line"></div>
        <div class="spotlights">
            <div class="light light-left"></div>
            <div class="light light-right"></div>
        </div>
    </div>

    <div class="container py-5 relative-z">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="tactical-board fade-in-up">
                    
                    <div class="board-header">
                        <div class="header-logo">
                            <i class="fas fa-futbol"></i> FIFA MANAGER
                        </div>
                        <h1 class="board-title">CRÉATION DE CAMPAGNE</h1>
                        <div class="board-status">
                            <span class="status-dot"></span> EN ATTENTE
                        </div>
                    </div>

                    <form action="{{ route('vente.votation.store') }}" method="POST">
                        @csrf
                        
                        <div class="board-body">
                            <div class="form-group-stadium mb-4">
                                <label>TITRE DU MATCH (SUJET)</label>
                                <div class="input-field">
                                    <i class="fas fa-trophy field-icon-left"></i>
                                    <input type="text" name="nom_theme" class="stadium-input" placeholder="Ex: JOUEUR DE L'ANNÉE 2026" required autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group-stadium mb-5">
                                <label>COUP DE SIFFLET FINAL (DATE)</label>
                                <div class="input-field">
                                    <i class="far fa-clock field-icon-left"></i>
                                    <input type="date" name="date_fermeture" class="stadium-input date-input-fix" required>
                                </div>
                            </div>

                            <div class="actions-footer">
                                <a href="{{ route('vente.votation.list') }}" class="btn-bench">
                                    <i class="fas fa-arrow-left me-2"></i> RETOUR VESTIAIRE
                                </a>
                                
                                <button type="submit" class="btn-kickoff">
                                    LANCER LA CAMPAGNE <i class="fas fa-check ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

<style>
    :root {
        --grass-dark: #1a4d1a;
        --grass-light: #225c22;
        --neon-stadium: #00ff88;
        --panel-bg: #1e2124;
    }

    .stadium-wrapper {
        min-height: 100vh;
        background-color: #0f1215;
        overflow: hidden;
        position: relative;
        font-family: 'Montserrat', sans-serif;
    }

    .pitch-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, #0a0c10 0%, #1a4d1a 100%);
        z-index: 0;
        perspective: 1000px;
    }

    .grass-pattern {
        position: absolute; bottom: 0; width: 100%; height: 60%;
        background-image: repeating-linear-gradient(
            90deg,
            var(--grass-dark) 0px,
            var(--grass-dark) 50px,
            var(--grass-light) 50px,
            var(--grass-light) 100px
        );
        transform: rotateX(60deg) scale(2);
        opacity: 0.6;
        box-shadow: inset 0 0 200px #000;
    }

    .spotlights { position: absolute; top: 0; width: 100%; height: 100%; pointer-events: none; }
    .light {
        position: absolute; top: -100px; width: 200px; height: 1000px;
        background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
        transform-origin: top center;
        filter: blur(20px);
    }
    .light-left { left: 20%; transform: rotate(25deg); animation: swayLeft 10s infinite ease-in-out; }
    .light-right { right: 20%; transform: rotate(-25deg); animation: swayRight 12s infinite ease-in-out; }

    @keyframes swayLeft { 0%, 100% { transform: rotate(25deg); } 50% { transform: rotate(15deg); } }
    @keyframes swayRight { 0%, 100% { transform: rotate(-25deg); } 50% { transform: rotate(-15deg); } }

    .relative-z { position: relative; z-index: 10; }

    .tactical-board {
        background: var(--panel-bg);
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        border: 2px solid #333;
        overflow: hidden;
        margin-top: 50px;
    }

    .board-header {
        background: #15171a;
        padding: 30px;
        border-bottom: 2px solid #333;
        display: flex; justify-content: space-between; align-items: center;
    }

    .header-logo { color: #888; font-weight: 700; letter-spacing: 2px; font-size: 0.8rem; }
    
    .board-title {
        color: white; font-weight: 900; margin: 0; font-size: 1.8rem;
        text-transform: uppercase;
        background: linear-gradient(to right, #fff, #bbb);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    .board-status { color: var(--neon-stadium); font-size: 0.8rem; font-weight: bold; }
    .status-dot { 
        display: inline-block; width: 8px; height: 8px; background: var(--neon-stadium); 
        border-radius: 50%; margin-right: 5px; animation: blink 2s infinite; 
    }

    .board-body { padding: 50px; background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png'); }

    .form-group-stadium label {
        color: #aaa; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 10px; display: block;
    }

    .input-field { position: relative; }
    
    .stadium-input {
        width: 100%; background: #2b2e33; border: 1px solid #444; color: white;
        padding: 15px 20px 15px 50px; 
        font-size: 1.1rem; border-radius: 8px; font-weight: 600;
        transition: 0.3s;
    }
    
    .stadium-input[type="date"] { color-scheme: dark; }
    .stadium-input::-webkit-calendar-picker-indicator {
        cursor: pointer; opacity: 0.6; transition: 0.2s;
    }
    .stadium-input::-webkit-calendar-picker-indicator:hover {
        opacity: 1; background-color: rgba(255,255,255,0.1); border-radius: 4px;
    }

    .stadium-input:focus {
        border-color: var(--neon-stadium); outline: none;
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.2); background: #32363b;
    }
    
    .field-icon-left {
        position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
        color: #666; font-size: 1.2rem; pointer-events: none;
    }

    .actions-footer {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);
    }

    .btn-bench {
        color: #888; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.3s;
        display: inline-flex; align-items: center;
    }
    .btn-bench:hover { color: white; transform: translateX(-5px); }

    .btn-kickoff {
        background: linear-gradient(135deg, var(--neon-stadium), #00cc6a);
        color: #00331a; border: none; padding: 15px 40px; border-radius: 50px;
        font-weight: 800; font-size: 1.1rem; letter-spacing: 1px;
        box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        transition: 0.3s; cursor: pointer; position: relative; z-index: 5;
    }
    .btn-kickoff:hover {
        transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 30px rgba(0, 255, 136, 0.5);
    }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    .fade-in-up { animation: fadeUp 0.8s ease-out forwards; }
</style>
@endsection
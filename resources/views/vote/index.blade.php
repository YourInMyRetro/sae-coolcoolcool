@extends('layout')

@section('content')
<div class="vote-page-wrapper" style="padding: 50px 0; background-color: #f9f9f9; min-height: 80vh;">
    <div class="container" style="text-align: center; max-width: 1000px;">
        
        <h1 style="color: #326295; font-size: 2.5rem; margin-bottom: 15px;">
            <i class="fas fa-trophy" style="color: #ffd700;"></i> Espace de Vote FIFA 
            <i class="fas fa-poll help-trigger" 
               style="font-size: 0.5em; vertical-align: middle; color: #326295; cursor: pointer;"
               data-title="Comment ça marche ?" 
               data-content="Ici, c'est VOUS le jury ! Pour chaque trophée, vous devrez sélectionner 3 candidats. Le 1er gagne 5 points, le 2ème 3 points et le 3ème 1 point. Votre voix compte pour 25% du résultat final !"
               data-link="{{ route('aide') }}#section-votes"></i>
        </h1>
        
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">
            Participez à l'histoire du football en élisant les meilleurs acteurs de la saison.
            <br>
            <span style="font-size: 0.9em; color: #e67e22;">
                <i class="fas fa-exclamation-triangle"></i> Attention, vous devez avoir un compte pour voter.
            </span>
        </p>

        @if(isset($votes) && count($votes) > 0)
            <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
                @foreach($votes as $vote) 
                    <div class="vote-card" style="width: 320px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; border: 1px solid #eee;">
                        
                        {{-- Image d'illustration du thème (si dispo, sinon placeholder) --}}
                        <div style="height: 180px; background: linear-gradient(135deg, #003366, #326295); display: flex; align-items: center; justify-content: center; color: white;">
                             @if($vote->nom_theme == 'The Best - Joueur de la FIFA')
                                <i class="fas fa-male" style="font-size: 5rem; opacity: 0.8;"></i>
                             @elseif($vote->nom_theme == 'The Best - Joueuse de la FIFA')
                                <i class="fas fa-female" style="font-size: 5rem; opacity: 0.8;"></i>
                             @elseif($vote->nom_theme == 'The Best - Gardien de but de la FIFA')
                                <i class="fas fa-hand-paper" style="font-size: 5rem; opacity: 0.8;"></i>
                             @elseif($vote->nom_theme == 'The Best - Entraîneur de la FIFA pour le football masculin')
                                <i class="fas fa-user-tie" style="font-size: 5rem; opacity: 0.8;"></i>
                             @else
                                <i class="fas fa-award" style="font-size: 5rem; opacity: 0.8;"></i>
                             @endif
                        </div>

                        <div style="padding: 25px;">
                            <h3 style="margin-top: 0; color: #333; font-size: 1.3rem; margin-bottom: 10px; height: 3.5rem; display: flex; align-items: center; justify-content: center;">
                                {{ $vote->nom_theme }}
                            </h3>
                            
                            <p style="color: #888; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="far fa-clock"></i> Fin des votes le : <br>
                                <strong>{{ $vote->date_fermeture ?? 'Non définie' }}</strong>
                                <i class="fas fa-info-circle help-trigger" 
                                   data-title="Date limite" 
                                   data-content="Après cette date, il sera trop tard ! Le formulaire sera désactivé."></i>
                            </p>

                            @auth
                                <a href="{{ route('vote.show', $vote->idtheme) }}" class="btn-vote-action" style="display: block; width: 100%; padding: 12px; background-color: #00cfb7; color: #003366; text-decoration: none; border-radius: 6px; font-weight: bold; text-transform: uppercase; transition: background 0.2s;">
                                    Je vote ! <i class="fas fa-chevron-right"></i>
                                </a>
                                <div style="margin-top: 10px;">
                                    <i class="fas fa-mouse-pointer help-trigger" 
                                       style="color: #00cfb7;"
                                       data-title="Accéder au scrutin" 
                                       data-content="Cliquez ici pour voir la liste des nominés et faire votre choix (Top 3)."></i>
                                </div>
                            @else
                                <a href="{{ route('login') }}" style="display: block; width: 100%; padding: 12px; background-color: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; cursor: not-allowed;">
                                    <i class="fas fa-lock"></i> Se connecter
                                </a>
                                <div style="margin-top: 10px;">
                                    <i class="fas fa-key help-trigger" 
                                       style="color: #e74c3c;"
                                       data-title="Connexion requise" 
                                       data-content="Pour éviter les tricheries, vous devez vous connecter à votre compte FIFA pour pouvoir voter."></i>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: inline-block;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                <h3>Aucun vote en cours actuellement.</h3>
                <p>Revenez plus tard pour les prochaines élections !</p>
            </div>
        @endif
        
        <div style="margin-top: 50px; background: white; padding: 30px; border-radius: 8px; border: 1px solid #eee; text-align: left; max-width: 800px; margin-left: auto; margin-right: auto;">
            <h3 style="color: #326295; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <i class="fas fa-balance-scale"></i> Règlement rapide
            </h3>
            <ul style="color: #555; line-height: 1.8;">
                <li>Vous ne pouvez voter qu'une seule fois par catégorie.</li>
                <li>Votre vote est <strong>définitif</strong> après validation.</li>
                <li>Les insultes ou propos discriminatoires dans les commentaires entraînent la suppression du compte.</li>
            </ul>
        </div>

    </div>
</div>
@endsection
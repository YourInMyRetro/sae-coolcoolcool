@extends('layout')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 text-light border-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour au Blog
        </a>
        <a href="{{ route('home') }}" class="btn btn-sm btn-site-home rounded-pill px-4">
            <i class="fas fa-home me-2"></i> Site Principal
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="article-hero">
                <img src="{{ $post->photo_presentation ? asset($post->photo_presentation) : asset('img/placeholder.jpg') }}" 
                     class="article-hero-img" alt="{{ $post->titre_publication }}"
                     onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="article-overlay">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">Officiel</span>
                        <span class="text-info text-uppercase fw-bold small">
                            {{ \Carbon\Carbon::parse($post->date_publication)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <h1 class="display-4 fw-bold text-white mb-2" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                        {{ $post->titre_publication }}
                    </h1>
                </div>
            </div>

            <div class="row justify-content-center mb-5">
                <div class="col-lg-10">
                    <div class="lead text-white fw-bold mb-4" style="border-left: 4px solid var(--fifa-cyan); padding-left: 20px;">
                        {!! nl2br(e($post->resume_publication)) !!}
                    </div>

                    @if($post->blog)
                        <div class="article-content">
                            {!! nl2br(e($post->blog->texte_blog)) !!}
                        </div>
                    @endif
                    
                    <hr class="my-5 border-secondary">
                </div>
            </div>

            <div class="comments-section" id="commentsArea">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="text-white fw-bold m-0">
                        Discussion <span class="fs-5 ms-2 text-muted">({{ $post->commentaires->count() }})</span>
                    </h3>
                    @if(!Auth::check())
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill">Se connecter</a>
                    @endif
                </div>

                @if(Auth::check())
                    <div class="d-flex mb-5">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-circle">
                                {{ substr(Auth::user()->prenom ?? 'M', 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <form action="{{ route('blog.comment.store', $post->id_publication) }}" method="POST">
                                @csrf
                                <textarea name="texte_commentaire" class="form-control bg-dark text-white border-secondary mb-2" rows="2" placeholder="Partagez votre avis..." required></textarea>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-fifa-cyan text-dark fw-bold rounded-pill">Publier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="comments-list">
                    @forelse($post->commentaires as $commentaire)
                        <div class="comment-wrapper">
                            
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-circle">
                                        {{ substr($commentaire->user->prenom ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="comment-content-box">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="comment-author">{{ $commentaire->user->prenom ?? 'Utilisateur' }} {{ $commentaire->user->nom ?? '' }}</span>
                                                <span class="comment-date">{{ \Carbon\Carbon::parse($commentaire->date_depot)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <p class="comment-text">{{ $commentaire->texte_commentaire }}</p>
                                    </div>

                                    @if(Auth::check())
                                        <div class="mt-1 ms-2">
                                            <button class="btn btn-link text-decoration-none text-muted p-0 small" style="font-size: 0.85rem;" onclick="toggleReply('{{ $commentaire->id_commentaire }}')">
                                                <i class="fas fa-reply me-1"></i> Répondre
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(Auth::check())
                                <div id="reply-form-{{ $commentaire->id_commentaire }}" class="reply-form-container" style="display: none;">
                                    <form action="{{ route('blog.comment.store', $post->id_publication) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="com_id_commentaire" value="{{ $commentaire->id_commentaire }}">
                                        <div class="d-flex gap-2">
                                            <div class="avatar-circle avatar-small me-2">
                                                {{ substr(Auth::user()->prenom ?? 'M', 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea name="texte_commentaire" class="form-control form-control-sm bg-dark text-white border-secondary mb-2" rows="2" placeholder="Répondre à {{ $commentaire->user->prenom }}..." required></textarea>
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="toggleReply('{{ $commentaire->id_commentaire }}')">Annuler</button>
                                                    <button type="submit" class="btn btn-sm btn-light fw-bold">Envoyer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if($commentaire->reponses->count() > 0)
                                <div class="replies-container">
                                    @foreach($commentaire->reponses as $reponse)
                                        <div class="reply-item d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-circle avatar-small">
                                                    {{ substr($reponse->user->prenom ?? 'U', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="reply-bubble">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-white fw-bold small">{{ $reponse->user->prenom }}</span>
                                                        <span class="comment-date small">{{ \Carbon\Carbon::parse($reponse->date_depot)->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="comment-text small mb-0">{{ $reponse->texte_commentaire }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="text-center py-4 text-muted border border-secondary border-dashed rounded-3">
                            <p class="mb-0">Aucun commentaire. Soyez le premier !</p>
                        </div>
                    @endforelse
                </div>
            </div>

<script>
    function toggleReply(id) {
        const form = document.getElementById('reply-form-' + id);
        
        // Vérification simple du style display
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block"; // Affiche
            
            // Focus automatique sur le champ texte
            const textarea = form.querySelector('textarea');
            if(textarea) textarea.focus();
        } else {
            form.style.display = "none"; // Cache
        }
    }
</script>
    </div>
</div>

<script>
    function toggleReplyForm(id) {
        const form = document.getElementById('reply-form-' + id);
        if (form.classList.contains('d-none')) {
            form.classList.remove('d-none');
            const textarea = form.querySelector('textarea');
            if(textarea) textarea.focus();
        } else {
            form.classList.add('d-none');
        }
    }
</script>
@endsection
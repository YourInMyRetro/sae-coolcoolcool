@extends('layout')

@section('content')
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-end mb-5 border-bottom border-secondary pb-4">
        <div>
            <div class="mb-3">
                <a href="{{ route('home') }}" class="btn btn-sm btn-site-home rounded-pill px-3">
                    <i class="fas fa-home me-2"></i> Retour au Site (Boutique, Votes...)
                </a>
            </div>
            <h6 class="text-uppercase fw-bold" style="color: var(--fifa-cyan); letter-spacing: 2px;">Inside FIFA</h6>
            <h1 class="display-4 text-white fw-bold mb-0">Actualités & Blog</h1>
        </div>
    </div>

    <div class="row g-5 blog-grid-gap">
        @forelse($posts as $post)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('blog.show', $post->id_publication) }}" class="text-decoration-none h-100 d-block">
                    <article class="blog-card h-100">
                        <div class="blog-card-img-wrapper">
                            <img src="{{ $post->photo_presentation ? asset($post->photo_presentation) : asset('img/placeholder.jpg') }}" 
                                 class="blog-card-img" 
                                 alt="{{ $post->titre_publication }}"
                                 onerror="this.src='{{ asset('img/placeholder.jpg') }}'"> <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge bg-dark text-white border border-secondary shadow-sm">
                                    <i class="fas fa-eye me-1"></i> {{ $post->commentaires->count() }} avis
                                </span>
                            </div>
                        </div>
                        
                        <div class="blog-card-body">
                            <div class="blog-date">
                                {{ \Carbon\Carbon::parse($post->date_publication)->translatedFormat('d F Y') }}
                            </div>
                            <h3 class="blog-title">{{ Str::limit($post->titre_publication, 50) }}</h3>
                            <p class="blog-excerpt">
                                {{ Str::limit($post->resume_publication, 100) }}
                            </p>
                            <div class="mt-auto d-flex align-items-center text-white fw-bold small">
                                Lire l'article <i class="fas fa-arrow-right ms-2" style="color: var(--fifa-cyan);"></i>
                            </div>
                        </div>
                    </article>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-dark p-5 rounded-3 border border-secondary border-dashed">
                    <i class="far fa-newspaper fa-4x mb-3 text-muted"></i>
                    <h3 class="text-white">Aucun article pour le moment</h3>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
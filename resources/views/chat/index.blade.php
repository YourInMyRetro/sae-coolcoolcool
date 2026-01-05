@extends('layout')

@section('content')
<style>
    ::placeholder { color: #cfcfcf !important; opacity: 1; }
    #chat-box::-webkit-scrollbar { width: 8px; }
    #chat-box::-webkit-scrollbar-track { background: #0f1623; }
    #chat-box::-webkit-scrollbar-thumb { background: #326295; border-radius: 4px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white fw-bold"><i class="fas fa-robot me-2" style="color: #00cfb7;"></i> Chatbot FIFA</h2>
                <div>
                    <button onclick="clearChat()" class="btn btn-sm btn-outline-danger me-2" title="Effacer la conversation">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <a href="{{ route('home') }}" class="text-white text-decoration-none"><i class="fas fa-times"></i> Fermer</a>
                </div>
            </div>

            <div class="card border-0 shadow-lg" style="height: 550px; display: flex; flex-direction: column; background: #1a202c; border-radius: 15px; overflow: hidden;">
                
                <div id="chat-box" class="card-body p-4" style="flex: 1; overflow-y: auto;">
                    <div class="text-center text-muted mt-5" id="loading-msg">
                        <i class="fas fa-circle-notch fa-spin fa-2x"></i><br><br>Connexion à l'IA...
                    </div>
                </div>

                <div class="card-footer p-3" style="background: #2d3748; border-top: 1px solid #4a5568;">
                    <form id="chat-form" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="message-input" class="form-control" 
                               style="background: #1a202c; border: 1px solid #4a5568; color: white; padding: 12px;" 
                               placeholder="Posez votre question sur le foot..." autocomplete="off">
                        <button type="submit" class="btn fw-bold" style="background: #00cfb7; color: #000; padding: 0 20px;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    let isFirstLoad = true;

    // Fonction pour vider le chat
    function clearChat() {
        if(!confirm('Voulez-vous vraiment effacer toute la conversation ?')) return;

        fetch("{{ route('chat.clear') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            }
        }).then(() => {
            chatBox.innerHTML = '';
            fetchMessages(); // Recharge pour afficher le message d'accueil
        });
    }

    function fetchMessages() {
        fetch("{{ route('chat.fetch') }}")
            .then(response => response.json())
            .then(data => {
                if(data.length === 0) {
                    // Message d'accueil par défaut
                    chatBox.innerHTML = `
                        <div class="d-flex justify-content-start mb-3">
                            <div style="max-width: 80%;">
                                <div class="p-3 rounded-3 text-dark fw-bold" style="background-color: #e2e8f0;">
                                    Bonjour ! ⚽<br>Je suis l'IA experte de la FIFA. Posez-moi une question !
                                </div>
                                <div class="text-muted small mt-1">Assistant FIFA</div>
                            </div>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.forEach(msg => {
                    const isMe = (msg.is_admin == 0);
                    const align = isMe ? 'end' : 'start';
                    const bg = isMe ? '#326295' : '#e2e8f0'; 
                    const textColor = isMe ? 'text-white' : 'text-dark fw-bold';
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                    html += `
                        <div class="d-flex justify-content-${align} mb-3">
                            <div style="max-width: 80%;">
                                <div class="p-3 rounded-3 ${textColor}" style="background-color: ${bg}; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    ${msg.content}
                                </div>
                                <div class="text-muted small text-${align} mt-1">${time}</div>
                            </div>
                        </div>
                    `;
                });
                
                if(chatBox.innerHTML !== html) {
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const content = messageInput.value;
        if(!content.trim()) return;

        messageInput.value = ''; 
        
        const tempHtml = `
            <div class="d-flex justify-content-end mb-3">
                <div style="max-width: 80%;">
                    <div class="p-3 rounded-3 text-white" style="background-color: #326295; opacity: 0.7;">
                        ${content} <i class="fas fa-sync fa-spin ms-2 small"></i>
                    </div>
                </div>
            </div>
        `;
        chatBox.insertAdjacentHTML('beforeend', tempHtml);
        chatBox.scrollTop = chatBox.scrollHeight;

        fetch("{{ route('chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ content: content })
        }).then(() => {
            fetchMessages(); 
        });
    });

    setInterval(fetchMessages, 3000);
    fetchMessages();
</script>
@endsection
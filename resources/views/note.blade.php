@php
    $isDue = false;
    $reminderText = '';
    $bellColor = 'secondary';
    
    if (!empty($note['reminder_at'])) {
        $reminderDate = \Carbon\Carbon::parse($note['reminder_at']);
        $now = \Carbon\Carbon::now();
        
        if ($reminderDate->isPast()) {
            $isDue = true;
            $bellColor = 'danger';
            $reminderText = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Atrasado (Venceu ' . $reminderDate->format('d/m H:i') . ')';
        } else if ($reminderDate->isToday()) {
            $isDue = true;
            $bellColor = 'warning';
            $reminderText = '<i class="fa-solid fa-clock me-1"></i> Vence hoje às ' . $reminderDate->format('H:i');
        } else {
            $bellColor = 'info';
            $reminderText = '<i class="fa-solid fa-calendar-day me-1"></i> Prazo: ' . $reminderDate->format('d/m/Y H:i');
        }
    }
@endphp

<div class="row mb-4">
    <div class="col">
        <!-- O cartão muda a borda se estiver vencendo/vencido -->
        <div class="card p-3 p-md-4 bg-body-tertiary {{ $isDue ? 'border border-2 border-danger shadow' : '' }}">
            <div class="row">
                <div class="col-12">
                    @if(isset($note['categories']) && count($note['categories']) > 0)
                        <div class="mb-2">
                            @foreach($note['categories'] as $cat)
                                <span class="badge text-bg-{{ $cat['color'] }} me-1">{{ $cat['name'] }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    <h3 class="note-title-custom">
                        @if(isset($note['is_protected']) && $note['is_protected']) 
                            <i class="fa-solid fa-lock text-warning me-2" title="Nota Protegida"></i> 
                        @endif 
                        {{ $note['title'] }}
                    </h3>
                    
                    <div class="mb-2">
                        <small class="text-secondary note-meta-custom">
                            <span class="opacity-75 me-1">Criado:</span>
                            <strong>{{ date('d/m/Y H:i', strtotime($note['created_at'])) }}</strong>
                            
                            @if($note['created_at'] !== $note['updated_at'])
                                <span class="mx-2 opacity-50">|</span>
                                <span class="opacity-75 me-1">Alterado:</span>
                                <strong>{{ date('d/m/Y H:i', strtotime($note['updated_at'])) }}</strong>
                            @endif
                        </small>
                    </div>

                    <!-- Exibe a etiqueta de Lembrete caso exista -->
                    @if(!empty($note['reminder_at']))
                        <br>
                        <small class="text-{{ $bellColor }} mt-1 d-inline-block fw-bold">
                            {!! $reminderText !!}
                        </small>
                    @endif
                </div>
                
                <div class="col-12 col-md-5 d-flex d-flex justify-content-end">
                    
                    <!-- Botão de Lembrete -->
                    <button type="button" class="btn btn-outline-{{ $bellColor }} btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#reminderModal{{ $note['id'] }}" title="Configurar Prazo">
                        <i class="fa-{{ empty($note['reminder_at']) ? 'regular' : 'solid' }} fa-bell"></i>
                    </button>

                    <a href="{{ route('exportPdf', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-danger btn-sm mx-1" title="Exportar PDF"><i class="fa-regular fa-file-pdf"></i></a>
                    
                    @if(!empty($note['public_id']))
                        <button type="button" class="btn btn-outline-info btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#shareModal{{ $note['id'] }}" title="Configurar Link Público">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    @endif
                    
                    <a href="{{ route('pin', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-{{ $note['is_pinned'] ? 'warning' : 'secondary' }} btn-sm mx-1"><i class="fa-solid fa-thumbtack"></i></a>
                    <a href="{{ route('edit', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-secondary btn-sm mx-1"><i class="fa-regular fa-pen-to-square"></i></a>
                    <a href="{{ route('delete', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-danger btn-sm mx-1"><i class="fa-regular fa-trash-can"></i></a>
                </div>
            </div>
            
            <div class="progress mt-3 mb-2 d-none checklist-progress" id="progress-{{ $note['id'] }}" style="height: 6px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <hr>
            
            @php 
                $isUnlocked = session()->has('unlocked_notes.'.$note['id']) && session('unlocked_notes.'.$note['id']) > now();
            @endphp

            @if(isset($note['is_protected']) && $note['is_protected'] && !$isUnlocked)
                <div class="text-center p-3 p-md-4 bg-dark rounded border border-warning" id="locked-state-{{ $note['id'] }}">
                    <i class="fa-solid fa-shield-halved text-warning display-4 mb-3"></i>
                    <h5 class="text-warning">Conteúdo Protegido</h5>
                    <p class="text-secondary small mb-4">Esta nota contém informações sensíveis e está no Cofre.</p>
                    <button class="btn btn-outline-warning px-5" data-bs-toggle="modal" data-bs-target="#unlockModal{{ $note['id'] }}">
                        <i class="fa-solid fa-key me-2"></i>Desbloquear para Ler
                    </button>
                </div>
                <div class="note-text-custom ql-snow d-none" id="unlocked-content-{{ $note['id'] }}">
                    <div class="ql-editor p-0" style="min-height: auto;"></div>
                </div>
            @else
                <div class="note-text-custom ql-snow">
                    <div class="ql-editor p-0" style="min-height: auto;">
                        {!! $note['text'] !!}
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</div>

<!-- Modal de Lembrete -->
<div class="modal fade" id="reminderModal{{ $note['id'] }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h6 class="modal-title"><i class="fa-regular fa-bell me-2"></i>Definir Prazo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 p-md-4">
                <label class="form-label text-secondary small">Escolha a data e a hora limite:</label>
                <!-- Formata a data para exibir corretamente no input nativo do navegador -->
                <input type="datetime-local" class="form-control mb-2 text-center" id="reminderInput{{ $note['id'] }}" 
                       value="{{ !empty($note['reminder_at']) ? date('Y-m-d\TH:i', strtotime($note['reminder_at'])) : '' }}">
            </div>
            <div class="modal-footer border-secondary justify-content-between">
                <!-- Se já tem lembrete, mostra botão de remover -->
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="saveReminder({{ $note['id'] }}, true)" {{ empty($note['reminder_at']) ? 'disabled' : '' }}>
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button type="button" class="btn btn-info px-4" onclick="saveReminder({{ $note['id'] }}, false)">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Compartilhamento (MANTIDO) -->
@if(!empty($note['public_id']))
<div class="modal fade" id="shareModal{{ $note['id'] }}" tabindex="-1" aria-hidden="true">
    <!-- ... HTML do seu modal de compartilhamento original ... -->
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Compartilhar Nota</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Link público desta nota:</p>
                <div class="input-group mb-1">
                    <input type="text" class="form-control bg-dark text-light border-secondary" id="publicLink{{ $note['id'] }}" value="{{ route('public.note', ['public_id' => $note['public_id']]) }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink({{ $note['id'] }})">
                        <i class="fa-regular fa-copy"></i> Copiar
                    </button>
                </div>
                <span id="copyMsg{{ $note['id'] }}" class="text-success small d-none mb-3 d-block">Link copiado para a área de transferência!</span>
                <hr class="border-secondary mt-4 mb-3">
                <label class="form-label">Tempo de Validade do Link:</label>
                <select class="form-select bg-dark text-light border-secondary" id="expireSelect{{ $note['id'] }}">
                    <option value="none" {{ empty($note['expires_at']) ? 'selected' : '' }}>Sem validade (Permanente)</option>
                    <option value="24h">24 Horas</option>
                    <option value="7d">7 Dias</option>
                </select>
                <span id="saveMsg{{ $note['id'] }}" class="text-success small d-none mt-2 d-block">Configuração salva!</span>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-info px-4" onclick="saveExpiration({{ $note['id'] }})">Salvar Validade</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal de Cofre (MANTIDO) -->
@if(isset($note['is_protected']) && $note['is_protected'])
<div class="modal fade" id="unlockModal{{ $note['id'] }}" tabindex="-1" aria-hidden="true">
    <!-- ... HTML do seu modal de cofre original ... -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-warning">
            <div class="modal-header border-warning text-warning">
                <h6 class="modal-title"><i class="fa-solid fa-lock me-2"></i>Acesso Restrito</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 p-md-4">
                <p class="small text-secondary mb-3">Confirme sua senha de acesso para abrir o cofre.</p>
                <input type="password" class="form-control mb-2 text-center" id="vaultPassword{{ $note['id'] }}" placeholder="Sua senha...">
                <span class="text-danger small d-none" id="vaultError{{ $note['id'] }}">Senha incorreta. Tente novamente.</span>
            </div>
            <div class="modal-footer border-warning justify-content-center">
                <button type="button" class="btn btn-warning w-100" onclick="unlockVault({{ $note['id'] }})">
                    <i class="fa-solid fa-unlock me-2"></i>Liberar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@once
<script>
    // ... [Funções antigas mantidas copyLink, saveExpiration, unlockVault] ...
    function copyLink(noteId) {
        var copyText = document.getElementById("publicLink" + noteId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        var msg = document.getElementById("copyMsg" + noteId);
        msg.classList.remove('d-none');
        setTimeout(() => { msg.classList.add('d-none'); }, 3000);
    }

    function saveExpiration(noteId) {
        var expireValue = document.getElementById("expireSelect" + noteId).value;
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var saveMsg = document.getElementById("saveMsg" + noteId);
        fetch('{{ route("setExpiration") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note_id: noteId, expires_in: expireValue })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                saveMsg.classList.remove('d-none');
                setTimeout(() => { saveMsg.classList.add('d-none'); }, 3000);
            } else { alert('Erro ao configurar validade.'); }
        }).catch(error => { console.error('Erro:', error); alert('Erro de comunicação.'); });
    }

    function unlockVault(noteId) {
        var pwdInput = document.getElementById("vaultPassword" + noteId);
        var errorSpan = document.getElementById("vaultError" + noteId);
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("unlockNote") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note_id: noteId, password: pwdInput.value })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('locked-state-' + noteId).classList.add('d-none');
                var myModalEl = document.getElementById('unlockModal' + noteId);
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if(modal) { modal.hide(); }
                var contentDiv = document.getElementById('unlocked-content-' + noteId);
                contentDiv.querySelector('.ql-editor').innerHTML = data.text;
                contentDiv.classList.remove('d-none');
                pwdInput.value = ''; 
                setTimeout(() => {
                    contentDiv.querySelector('.ql-editor').innerHTML = '';
                    contentDiv.classList.add('d-none');
                    document.getElementById('locked-state-' + noteId).classList.remove('d-none');
                }, 15 * 60 * 1000); 
            } else {
                errorSpan.classList.remove('d-none');
                pwdInput.value = '';
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    // NOVA FUNÇÃO DO LEMBRETE
    function saveReminder(noteId, remove = false) {
        var dateValue = document.getElementById("reminderInput" + noteId).value;
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Se clicou no botão da lixeira, enviamos nulo para limpar o prazo
        if(remove) { dateValue = null; }
        // Se tentou salvar sem escolher data, ignoramos
        else if(!dateValue) { alert('Escolha uma data e hora.'); return; }

        fetch('{{ route("setReminder") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({ 
                note_id: noteId, 
                reminder_at: dateValue 
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Como as bordas e cores dependem do backend (PHP/Carbon),
                // o jeito mais fácil e seguro de aplicar o novo visual é recarregando a página.
                window.location.reload(); 
            } else {
                alert('Erro ao salvar o lembrete.');
            }
        })
        .catch(error => console.error('Erro:', error));
    }
</script>
@endonce
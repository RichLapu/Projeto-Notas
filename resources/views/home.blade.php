@extends('layouts.main_layout')
@section('content')

<!-- CSS do Highlight.js para manter os blocos de código coloridos na Home -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">

<style>
    /* CSS para transformar a linha do checklist em um elemento interativo */
    .ql-editor li[data-list="checked"],
    .ql-editor li[data-list="unchecked"] {
        cursor: pointer !important;
        transition: all 0.2s ease;
        padding-top: 4px;
        padding-bottom: 4px;
        border-radius: 4px;
    }

    /* Efeito visual ao passar o mouse em cima da tarefa (Modo Claro) */
    .ql-editor li[data-list="checked"]:hover,
    .ql-editor li[data-list="unchecked"]:hover {
        background-color: rgba(0, 0, 0, 0.05); 
    }

    /* Efeito visual ao passar o mouse em cima da tarefa (Modo Escuro) */
    [data-bs-theme="dark"] .ql-editor li[data-list="checked"]:hover,
    [data-bs-theme="dark"] .ql-editor li[data-list="unchecked"]:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
</style>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">

                @include('top_bar')

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form id="searchForm" action="{{ route('home') }}" method="GET" class="d-flex flex-grow-1 me-4" onsubmit="return false;">
                        <input type="text" id="searchInput" name="search" class="form-control me-2 shadow-none" placeholder="Buscar notas..." value="{{ $search ?? '' }}" autocomplete="off">
                        <button type="button" id="clearBtn" class="btn btn-outline-secondary ms-2 d-none text-nowrap"><i class="fa-solid fa-eraser me-2"></i>Limpar</button>
                    </form>

                    <a href="{{ route('new') }}" class="btn btn-secondary px-3 text-nowrap">
                        <i class="fa-regular fa-pen-to-square me-2"></i>Criar Nota!
                    </a>
                </div>

                @php
                    $allCategories = \App\Models\Category::all();
                @endphp
                @if($allCategories->count() > 0)
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                        <span class="text-secondary small me-1"><i class="fa-solid fa-filter"></i> Filtrar:</span>
                        <span class="badge rounded-pill bg-secondary filter-pill px-3 py-2 shadow-sm" style="cursor: pointer;" data-category="">Todas</span>
                        
                        @foreach($allCategories as $cat)
                            <span class="badge rounded-pill text-bg-{{ $cat->color }} filter-pill px-3 py-2 shadow-sm" style="cursor: pointer;" data-category="{{ $cat->name }}">
                                {{ $cat->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if(count($notes) == 0)
                    <div class="row mt-5">
                        <div class="col text-center">
                            @if(!empty($search))
                                <p class="display-6 mb-5 text-secondary opacity-50">Nenhuma nota encontrada para "{{ $search }}"!</p>
                            @else
                                <p class="display-6 mb-5 text-secondary opacity-50">Você não tem notas disponíveis!</p>
                                <a href="{{ route('new') }}" class="btn btn-secondary btn-lg p-3 px-5">
                                    <i class="fa-regular fa-pen-to-square me-3"></i>Crie sua primeira nota!
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div id="notes-container">
                        @foreach($notes as $note)
                            <div class="note-wrapper" data-id="{{ $note['id'] }}">
                                @include('note')
                            </div>
                        @endforeach
                    </div>

                    <div id="no-results-msg" class="row mt-5" style="display: none;">
                        <div class="col text-center">
                            <p class="display-6 mb-5 text-secondary opacity-50">Nenhuma nota corresponde à busca local.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Live Search Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearBtn');
            const noteWrappers = document.querySelectorAll('.note-wrapper');
            const noResultsMsg = document.getElementById('no-results-msg');
            let currentCategory = ''; 

            function filterNotes() {
                if (!searchInput) return;

                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                if (searchTerm.length > 0 || currentCategory !== '') {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }

                noteWrappers.forEach(wrapper => {
                    const text = wrapper.innerText.toLowerCase();
                    
                    const matchesSearch = text.includes(searchTerm);
                    const matchesCategory = currentCategory === '' || text.includes(currentCategory.toLowerCase());
                    
                    if (matchesSearch && matchesCategory) {
                        wrapper.style.display = ''; 
                        visibleCount++;
                    } else {
                        wrapper.style.display = 'none'; 
                    }
                });

                if (noResultsMsg) {
                    noResultsMsg.style.display = (visibleCount === 0 && noteWrappers.length > 0) ? 'block' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    if (this.value === '') currentCategory = '';
                    filterNotes();
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    currentCategory = '';
                    filterNotes();
                    searchInput.focus();
                });
            }

            document.querySelectorAll('.note-wrapper .badge').forEach(badge => {
                badge.style.cursor = 'pointer'; 
                badge.addEventListener('click', function() {
                    currentCategory = this.innerText.trim();
                    if (searchInput) searchInput.value = currentCategory; 
                    filterNotes();
                });
            });

            document.querySelectorAll('.filter-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    currentCategory = this.getAttribute('data-category');
                    
                    if (currentCategory === '') {
                        if (searchInput) searchInput.value = ''; 
                    } else {
                        if (searchInput) searchInput.value = currentCategory; 
                    }
                    
                    filterNotes();
                });
            });

            // Lógica inicial das barras de progresso ao carregar a página
            document.querySelectorAll('.note-wrapper').forEach(wrapper => {
                const textContainer = wrapper.querySelector('.note-text-custom');
                if(!textContainer) return;

                const checked = textContainer.querySelectorAll('li[data-list="checked"]').length;
                const unchecked = textContainer.querySelectorAll('li[data-list="unchecked"]').length;
                const total = checked + unchecked;

                if (total > 0) {
                    const percent = Math.round((checked / total) * 100);
                    const progressBarContainer = wrapper.querySelector('.checklist-progress');
                    const progressBar = progressBarContainer.querySelector('.progress-bar');

                    progressBarContainer.classList.remove('d-none');
                    progressBar.style.width = percent + '%';
                    progressBar.setAttribute('aria-valuenow', percent);

                    if (percent === 100) progressBar.className = 'progress-bar bg-success';
                    else if (percent > 50) progressBar.className = 'progress-bar bg-info';
                    else progressBar.className = 'progress-bar bg-warning';
                }
            });
        });
    </script>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notesContainer = document.getElementById('notes-container');
        
        if (notesContainer) {
            new Sortable(notesContainer, {
                animation: 150, 
                ghostClass: 'opacity-50',
                
                // ESTA É A CORREÇÃO: Impede que o Drag-and-Drop engula os cliques dentro do texto ou botões
                filter: '.ql-editor, a, button', 
                preventOnFilter: false, 
                
                onEnd: function () {
                    let order = [];
                    document.querySelectorAll('.note-wrapper').forEach(function(el) {
                        order.push(el.getAttribute('data-id'));
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route("updateOrder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status !== 'success') console.error('Falha ao salvar a nova ordem.');
                    })
                    .catch(error => console.error('Erro na requisição:', error));
                }
            });
        }
    });

    // ---------------------------------------------------------
    // A MÁGICA DO CLIQUE: Ouvinte Global (Garante execução 100%)
    // ---------------------------------------------------------
    document.addEventListener('click', function(e) {
        
        // 1. Verifica se o clique foi em um elemento de checklist
        const li = e.target.closest('.ql-editor li[data-list]');
        if (!li) return; // Se clicou fora do checklist, ignora a ação

        // 2. Prevenções de segurança
        if (e.target.closest('a')) return; // Não interfere se o usuário clicar num link que estiver dentro da tarefa
        if (window.getSelection().toString().length > 0) return; // Não interfere se o usuário estiver arrastando o mouse para copiar o texto

        // 3. Inverte o status visual da caixinha
        const isChecked = li.getAttribute('data-list') === 'checked';
        li.setAttribute('data-list', isChecked ? 'unchecked' : 'checked');
        
        // 4. Localiza os containers
        const wrapper = li.closest('.note-wrapper');
        const textContainer = wrapper.querySelector('.ql-editor');
        
        // 5. Recalcula a barra de progresso
        const checkedCount = textContainer.querySelectorAll('li[data-list="checked"]').length;
        const uncheckedCount = textContainer.querySelectorAll('li[data-list="unchecked"]').length;
        const total = checkedCount + uncheckedCount;
        
        if (total > 0) {
            const percent = Math.round((checkedCount / total) * 100);
            const progressBarContainer = wrapper.querySelector('.checklist-progress');
            const progressBar = wrapper.querySelector('.progress-bar');
            
            if(progressBarContainer && progressBar) {
                progressBarContainer.classList.remove('d-none');
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                
                if (percent === 100) progressBar.className = 'progress-bar bg-success';
                else if (percent > 50) progressBar.className = 'progress-bar bg-info';
                else progressBar.className = 'progress-bar bg-warning';
            }
        }
        
        // 6. Salva as mudanças silenciosamente no Banco de Dados
        const noteId = wrapper.getAttribute('data-id');
        const newText = textContainer.innerHTML; 
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        if(noteId && csrfToken) {
            fetch('{{ route("toggleChecklist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ note_id: noteId, text_note: newText })
            }).catch(err => console.error('Erro ao salvar checklist:', err));
        }
    });
</script>

@endsection
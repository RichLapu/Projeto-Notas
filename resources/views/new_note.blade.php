@extends('layouts.main_layout')
@section('content')

<!-- CSS do Highlight.js (Tema Atom One Dark) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">

<style>
    /* Ajuste de contraste para a barra do Quill no tema escuro */
    [data-bs-theme="dark"] .ql-toolbar.ql-snow {
        background-color: #f8f9fa;
        border-color: #6c757d;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    [data-bs-theme="dark"] .ql-container.ql-snow {
        border-color: #6c757d;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    [data-bs-theme="dark"] .ql-editor.ql-blank::before {
        color: rgba(255, 255, 255, 0.5);
    }
    /* Arredondamento extra para os blocos de código */
    .ql-editor pre.ql-syntax {
        border-radius: 8px;
        padding: 15px;
    }

    /* -------------------------------------
       Modo Leitura (Distraction-Free) 
       ------------------------------------- */
    body.df-active {
        overflow: hidden; 
    }
    body.df-active .hide-in-df,
    body.df-active .ql-toolbar {
        display: none !important; 
    }
    body.df-active .df-fullscreen-card {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1050;
        border-radius: 0;
        border: none !important;
        padding: 5% 20% !important; 
        overflow-y: auto;
    }
    body.df-active #editor {
        height: auto !important;
        border: none !important;
        font-size: 1.15rem; 
        line-height: 1.8;
    }
    .df-only-btn {
        display: none;
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1060;
        border-radius: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }
    body.df-active .df-only-btn {
        display: inline-block;
    }
    @media (max-width: 768px) {
        body.df-active .df-fullscreen-card {
            padding: 5% 5% !important;
        }
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col">

            <div class="hide-in-df">
                @include('top_bar')
            </div>

            <div class="card p-5 bg-body-tertiary border border-secondary df-fullscreen-card">
                
                <div class="row mb-4 align-items-center hide-in-df">
                    <div class="col d-flex align-items-center">
                        <p class="display-6 mb-0">Nova Nota</p>
                        
                        <button type="button" id="df-toggle-btn" class="btn btn-outline-info btn-sm ms-4 mt-2">
                            <i class="fa-solid fa-expand me-2"></i>Modo Foco
                        </button>
                    </div>
                    <div class="col text-end">
                        <a href="{{ route('home') }}" class="btn btn-outline-danger">
                            <i class="fa-solid fa-xmark"></i>
                        </a>            
                    </div>
                </div>

                <form action="{{ route('newNoteSubmit') }}" method="post">
                    @csrf
                    
                    <div class="row">
                        <div class="col">
                            
                            <div class="hide-in-df">
                                <div class="mb-3">
                                    <label class="form-label">Note Title</label>
                                    <input type="text" class="form-control" name="text_title" value="{{ old('text_title') }}">
                                    @error('text_title')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categorias</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        @foreach($categories as $category)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="cat_{{ $category->id }}">
                                                <label class="form-check-label badge text-bg-{{ $category->color }}" for="cat_{{ $category->id }}">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_protected" name="is_protected" value="1">
                                    <label class="form-check-label text-warning fw-bold" for="is_protected" style="cursor: pointer;">
                                        <i class="fa-solid fa-lock me-1"></i> Proteger nota (Cofre)
                                    </label>
                                    <div class="form-text text-secondary"><i class="fa-solid fa-circle-info me-1"></i>O conteúdo desta nota será ocultado na tela inicial e exigirá sua senha para leitura.</div>
                                </div>
                                <label class="form-label">Note Text</label>
                            </div>
                            
                            <div class="mb-3">
                                <input type="hidden" name="text_note" id="text_note" value="{{ old('text_note') }}">
                                
                                <div id="editor" class="bg-body text-body" style="height: 300px;">
                                    {!! old('text_note') !!}
                                </div>
                                
                                @error('text_note')
                                    <div class="text-danger mt-1 hide-in-df">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3 hide-in-df">
                        <div class="col text-end">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary px-5"><i class="fa-solid fa-ban me-2"></i>Cancelar</a>
                            <button type="submit" class="btn btn-secondary px-5"><i class="fa-regular fa-circle-check me-2"></i>Criar</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <button type="button" id="df-exit-btn" class="btn btn-info px-4 py-2 df-only-btn">
                <i class="fa-solid fa-compress me-2"></i>Sair do Modo Foco (Esc)
            </button>

        </div>
    </div>
</div>

<!-- Script do Highlight.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Configuração do Modo Leitura
        const toggleBtn = document.getElementById('df-toggle-btn');
        const exitBtn = document.getElementById('df-exit-btn');
        const bodyClass = document.body.classList;

        function toggleFocusMode() {
            bodyClass.toggle('df-active');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', toggleFocusMode);
        if (exitBtn) exitBtn.addEventListener('click', toggleFocusMode);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && bodyClass.contains('df-active')) {
                toggleFocusMode();
            }
        });

        // 2. Configuração do Quill e Imagens
        function imageHandler() {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = () => {
                var file = input.files[0];
                var formData = new FormData();
                formData.append('image', file);
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route("uploadImage") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        var range = quill.getSelection();
                        quill.insertEmbed(range.index, 'image', data.url);
                    }
                })
                .catch(error => {
                    console.error('Erro no upload da imagem:', error);
                    alert('Erro ao enviar imagem para a nuvem.');
                });
            };
        }

        // Inicializa o Highlight.js no Quill (COM SUPORTE A CHECKLISTS)
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                syntax: true, // Habilita o Syntax Highlighting
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }], // <-- Checklists adicionados aqui
                        ['image', 'link', 'code-block'], 
                        ['clean']
                    ],
                    handlers: { image: imageHandler }
                }
            }
        });

        // 3. Submit Tradicional
        document.querySelector('form').onsubmit = function() {
            document.querySelector('#text_note').value = quill.root.innerHTML;
        };
        
    });
</script>

@endsection
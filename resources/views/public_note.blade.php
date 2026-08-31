@extends('layouts.main_layout')
@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="display-6 mb-0"><i class="fa-solid fa-book-open text-primary me-3"></i>Nota Compartilhada</p>
                
                {{-- Oculta o botão de tema se for gerado por PDF --}}
                @if(!isset($is_pdf))
                    <button id="theme-toggle" class="btn btn-outline-secondary">
                        <i id="theme-icon" class="fa-solid fa-moon"></i> Tema
                    </button>
                @endif
            </div>

            <div class="card p-5 bg-body-tertiary border border-secondary shadow-sm">
                @if(count($note->categories) > 0)
                    <div class="mb-3">
                        @foreach($note->categories as $cat)
                            <span class="badge text-bg-{{ $cat->color }} me-1">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                @endif
                
                <h1 class="mb-1" style="color: var(--bs-body-color);">{{ $note->title }}</h1>
                <small class="text-secondary d-block mb-4">
                    Escrito por <strong>{{ $note->user->username ?? 'Usuário' }}</strong> em {{ date('d/m/Y', strtotime($note->created_at)) }}
                </small>
                
                <hr class="mb-4">
                
                <div class="note-text-custom" style="color: var(--bs-body-color);">
                    {!! $note->text !!}
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection
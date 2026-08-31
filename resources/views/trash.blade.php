@extends('layouts.main_layout')
@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col">

            @include('top_bar')

            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="display-6 mb-0 text-secondary"><i class="fa-solid fa-trash-can me-3"></i>Lixeira</p>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">Voltar</a>
            </div>

            @if(count($notes) == 0)
                <div class="row mt-5">
                    <div class="col text-center">
                        <p class="display-6 mb-5 text-secondary opacity-50">Sua lixeira está vazia!</p>
                    </div>
                </div>
            @else
                @foreach($notes as $note)
                    <div class="row mb-4">
                        <div class="col">
                            <div class="card p-4 bg-body-tertiary border border-secondary">
                                <div class="row">
                                    <div class="col">
                                        <h3 class="note-title-custom text-secondary text-decoration-line-through">{{ $note['title'] }}</h3>
                                        <small class="text-secondary note-meta-custom">
                                            <span class="opacity-75 me-2">Apagado em:</span>
                                            <strong>{{ date('d/m/Y H:i:s', strtotime($note['deleted_at'])) }}</strong>
                                        </small>
                                    </div>
                                    <div class="col text-end">
                                        <a href="{{ route('restore', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-success btn-sm mx-1" title="Restaurar">
                                            <i class="fa-solid fa-arrow-rotate-left"></i> Restaurar
                                        </a>
                                        <a href="{{ route('forceDelete', ['id' => Crypt::encrypt($note['id'])]) }}" class="btn btn-outline-danger btn-sm mx-1" title="Excluir Definitivamente">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection
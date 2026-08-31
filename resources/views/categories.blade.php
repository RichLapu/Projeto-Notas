@extends('layouts.main_layout')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            @include('top_bar')

            <!-- Add New Category -->
            <div class="card p-4 bg-body-tertiary border border-secondary mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Nova Categoria</h5>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Voltar</a>
                </div>
                
                <form action="{{ route('categories.store') }}" method="POST" class="d-flex gap-3 align-items-center">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="Nome da Categoria" required>
                    <select name="color" class="form-select" required style="max-width: 200px;">
                        <option value="primary">Azul (Primary)</option>
                        <option value="secondary">Cinza (Secondary)</option>
                        <option value="success">Verde (Success)</option>
                        <option value="danger">Vermelho (Danger)</option>
                        <option value="warning">Amarelo (Warning)</option>
                        <option value="info">Ciano (Info)</option>
                        <option value="dark">Preto (Dark)</option>
                    </select>
                    <button type="submit" class="btn btn-secondary text-nowrap">
                        <i class="fa-solid fa-plus me-2"></i>Adicionar
                    </button>
                </form>
            </div>

            <!-- List Categories -->
            <div class="card p-4 bg-body-tertiary border border-secondary">
                <h5 class="mb-3">Categorias Existentes</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Cor de Exibição</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $category->color }} px-3 py-2">
                                            {{ $category->name }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        
                                        <!-- Edit Button (Opens Modal) -->
                                        <button type="button" class="btn btn-sm btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta categoria? As notas que a utilizam perderão esta tag.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                                <!-- Edit Modal for this specific category -->
                                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-dark text-light border-secondary">
                                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">Editar Categoria</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label">Nome</label>
                                                    <input type="text" name="name" class="form-control mb-3" value="{{ $category->name }}" required>
                                                    
                                                    <label class="form-label">Cor</label>
                                                    <select name="color" class="form-select" required>
                                                        <option value="primary" @if($category->color == 'primary') selected @endif>Azul</option>
                                                        <option value="secondary" @if($category->color == 'secondary') selected @endif>Cinza</option>
                                                        <option value="success" @if($category->color == 'success') selected @endif>Verde</option>
                                                        <option value="danger" @if($category->color == 'danger') selected @endif>Vermelho</option>
                                                        <option value="warning" @if($category->color == 'warning') selected @endif>Amarelo</option>
                                                        <option value="info" @if($category->color == 'info') selected @endif>Ciano</option>
                                                        <option value="dark" @if($category->color == 'dark') selected @endif>Preto</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-info px-4">Salvar Alterações</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
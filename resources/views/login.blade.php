@extends('layouts.main_layout')
@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        
        <!-- Cartão principal com limite de largura e bordas arredondadas -->
        <div class="card shadow-lg border-0 overflow-hidden" style="max-width: 900px; width: 100%; border-radius: 1rem;">
            <div class="row g-0">
                
                <!-- Lado Esquerdo: Branding -->
                <div class="col-md-5 bg-white text-dark d-none d-md-flex flex-column justify-content-center align-items-center p-5 text-center">
                    <!-- Logo original (removido o limite de height para manter proporção e qualidade real) -->
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Notas Logo" class="mb-3 img-fluid">
                    
                    <!-- Adicionado text-nowrap para não quebrar linha -->
                    <p class="small text-dark opacity-75 mb-0 fw-semibold text-nowrap">Sistema Integrado de Anotações e Lembretes</p>
                </div>

                <!-- Lado Direito: Formulário -->
                <div class="col-md-7 p-4 p-md-5 bg-body-tertiary">
                    
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Notas Logo" class="d-md-none mb-3 img-fluid">
                        <h4 class="fw-bold">Bem-vindo(a)</h4>
                        <p class="text-secondary small">Insira suas credenciais para acessar o painel</p>
                    </div>

                    <form action="/loginSubmit" method="post" novalidate>
                        @csrf
                        
                        <!-- Input Usuário -->
                        <div class="mb-3">
                            <label for="text_username" class="form-label small fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">USUÁRIO</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body text-secondary border-end-0"><i class="fa-regular fa-user"></i></span>
                                <input type="text" class="form-control bg-body border-start-0 ps-0 shadow-none" name="text_username" value="{{ old('text_username') }}" required>
                            </div>
                            @error('text_username')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Input Senha -->
                        <div class="mb-4">
                            <label for="text_password" class="form-label small fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">SENHA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body text-secondary border-end-0"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control bg-body border-start-0 ps-0 shadow-none" name="text_password" value="{{ old('text_password') }}" required>
                            </div>
                            @error('text_password')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Extras (Lembrar / Esqueci a senha) -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">Lembrar meu acesso</label>
                            </div>
                            <a href="#" class="text-decoration-none small fw-bold text-primary">Esqueceu a senha?</a>
                        </div>

                        <!-- Botão Branco (btn-light) com texto escuro (text-dark) -->
                        <button type="submit" class="btn btn-light text-dark border w-100 py-2 fw-bold">Acessar</button>
                    </form>

                    {{-- Alerta de Login Inválido --}}
                    @if(session('loginError'))
                        <div class="alert alert-danger text-center mt-4 small p-2">
                            {{ session('loginError') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
        
    </div>
@endsection
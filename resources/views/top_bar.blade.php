<div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3 mb-3">
    
    <!-- Logo -->
    <div class="text-center text-lg-start">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Notes logo" style="max-height: 45px;">
        </a>
    </div>

    <!-- Usuário e Botões -->
    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
        
        <!-- Usuário -->
        <span class="d-flex align-items-center text-secondary fw-semibold">
            <i class="fa-solid fa-user-circle fa-lg me-2"></i>
            {{ session('user.username') }}
        </span>

        <!-- Botões -->
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary flex-fill text-nowrap">
                <i class="fa-solid fa-tags"></i> Categorias
            </a>

            <a href="{{ route('trash') }}" class="btn btn-outline-secondary flex-fill text-nowrap">
                <i class="fa-solid fa-trash-can"></i> Lixeira
            </a>
            
            <button id="theme-toggle" class="btn btn-outline-secondary flex-fill text-nowrap">
                <i id="theme-icon" class="fa-solid fa-moon"></i> Tema
            </button>
            
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary flex-fill text-nowrap">
                Sair <i class="fa-solid fa-arrow-right-from-bracket ms-1"></i>
            </a>
        </div>
        
    </div>
</div>

<hr>
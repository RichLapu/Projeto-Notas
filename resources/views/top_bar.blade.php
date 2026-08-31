<div class="row mb-3 align-items-center">
    <div class="col">
        <a href="{{ route('home') }}">
            <!--<img src="{{ asset('assets/images/logo.png') }}" alt="Notes logo">-->
        </a>
    </div>
    <div class="col text-center">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Notes logo">
        </a>
    </div>
    <div class="col text-end d-flex justify-content-end align-items-center gap-3">
    
    <!-- Usuário -->
    <span class="d-flex align-items-center">
        <i class="fa-solid fa-user-circle fa-lg text-secondary me-2"></i>
        {{ session('user.username') }}
    </span>

    <!-- Botões -->
    <div class="d-flex align-items-center gap-2">

    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary text-nowrap" style="width: 120px;">
        <i class="fa-solid fa-tags"></i> Categorias
    </a>

    <a href="{{ route('trash') }}" class="btn btn-outline-secondary text-nowrap" style="width: 100px;">
        <i class="fa-solid fa-trash-can"></i> Lixeira
    </a>
    
    <button id="theme-toggle" class="btn btn-outline-secondary text-nowrap" style="width: 100px;">
        <i id="theme-icon" class="fa-solid fa-moon"></i> Tema
    </button>
    
    <a href="{{ route('logout') }}" class="btn btn-outline-secondary text-nowrap" style="width: 100px;">
        Sair <i class="fa-solid fa-arrow-right-from-bracket ms-1"></i>
    </a>
    
</div>
</div>
</div>

<hr>
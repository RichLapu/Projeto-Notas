<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Notas</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome via CDN Oficial (Resolve o problema dos ícones sumidos na Vercel) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
    /* Força as imagens inseridas pelo Quill a serem responsivas */
    .card img, .ql-editor img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="text-center py-4 mt-auto">
        <small class="text-secondary">
            &copy; {{ date('Y') }} Desenvolvido por Richard Lapuente. Todos os direitos reservados.
        </small>
    </footer>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const htmlElement = document.documentElement;
            const themeBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            const currentTheme = localStorage.getItem('theme') || 'dark';
            htmlElement.setAttribute('data-bs-theme', currentTheme);
            updateIcon(currentTheme);

            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const isDark = htmlElement.getAttribute('data-bs-theme') === 'dark';
                    const newTheme = isDark ? 'light' : 'dark';
                    
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateIcon(newTheme);
                });
            }

            function updateIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.classList.remove('fa-sun');
                        themeIcon.classList.add('fa-moon');
                    } else {
                        themeIcon.classList.remove('fa-moon');
                        themeIcon.classList.add('fa-sun');
                    }
                }
            }
        });
    </script>
</body>
</html>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'Login') - {{ config('app.name') }}</title>
    
    <!-- CSS files -->
    <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/demo.min.css') }}" rel="stylesheet"/>
    
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    
    @stack('styles')
</head>

<body class="d-flex flex-column">
    <script src="{{ asset('js/demo-theme.min.js') }}"></script>
    
    <div class="page page-center">
        <div class="container container-tight py-4">
            @yield('content')
        </div>
    </div>
    
    <!-- Tabler Core -->
    <script src="{{ asset('js/tabler.min.js') }}" defer></script>
    
    @stack('scripts')
</body>
</html>
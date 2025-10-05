<!DOCTYPE html>
<html>
<head>
    <title>{{ __('home.title') }}</title>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
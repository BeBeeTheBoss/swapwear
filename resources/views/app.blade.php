<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>{{ env('APP_NAME') ? env('APP_NAME') : 'Vintage' }}</title>
    <link rel="shortcut icon" href="{{ asset('images/bag.png') }}" type="image/x-icon">
    @vite('resources/js/app.js')
    @routes
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>

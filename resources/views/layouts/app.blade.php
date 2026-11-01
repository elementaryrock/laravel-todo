<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meridian Almanac — Personal Task Workspace')</title>
    <link rel="preload" href="/fonts/Chivo-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/Chivo-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/AzeretMono-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/css/almanac.css">
</head>
<body>
    @yield('content')
</body>
</html>


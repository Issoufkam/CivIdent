<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Document simulé</h1>
    <ul>
        @foreach ($metadata as $key => $value)
            <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
        @endforeach
    </ul>
</body>
</html>

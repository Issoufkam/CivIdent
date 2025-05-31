<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Soumission</title>
    {{-- Incluez ici les liens CSS de votre layout principal si nécessaire pour le style --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> {{-- ou le chemin vers votre CSS --}}
</head>
<body>
    <div class="container py-5">
        <h1>Formulaire de Test</h1>
        <form action="{{ route('test.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="test_field" class="form-label">Champ de Test</label>
                <input type="text" class="form-control" id="test_field" name="test_field" value="Ceci est un test">
            </div>
            <button type="submit" class="btn btn-primary">Soumettre le Test</button>
        </form>
    </div>
</body>
</html>

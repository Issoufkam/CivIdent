@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Actes</h1>
        <a href="{{ route('agent.actes.create') }}" class="btn btn-primary">Créer un Acte</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actes as $acte)
                    <tr>
                        <td>{{ $acte->id }}</td>
                        <td>{{ $acte->description }}</td>
                        <td>
                            <a href="{{ route('agent.actes.create', $acte->id) }}" class="btn btn-warning">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

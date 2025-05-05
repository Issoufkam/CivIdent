@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Liste des Agents</h1>
        <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">Ajouter un Agent</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $agent)
                    <tr>
                        <td>{{ $agent->id }}</td>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->email }}</td>
                        <td>
                            <a href="{{ route('admin.agents.edit', $agent->id) }}" class="btn btn-warning">Modifier</a>
                            <form action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Paiements</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Montant</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiements as $paiement)
                    <tr>
                        <td>{{ $paiement->id }}</td>
                        <td>{{ $paiement->amount }}</td>
                        <td>{{ $paiement->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

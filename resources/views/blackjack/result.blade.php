@extends('auth.layouts')

@section('content')
    <div class="container">
        <h1>Final Result</h1>
        <p>{{ $resultMessage }}</p>

        <h3>Your Hand:</h3>
        <p>Total: {{ $playerTotal }}</p>
        @foreach ($playerHand as $card)
            <img src="{{ asset($card['image']) }}" alt="{{ $card['rank'] }} of {{ $card['suit'] }}">
        @endforeach

        <h3>Dealer's Hand:</h3>
        <p>Total: {{ $dealerTotal }}</p>
        @foreach ($dealerMoves as $card)
            <img src="{{ asset($card['image']) }}" alt="{{ $card['rank'] }} of {{ $card['suit'] }}">
        @endforeach

        <p>Your updated balance: ${{ $user->balance }}</p>

        <a href="{{ route('blackjack.index') }}" class="btn btn-primary">Back to Game</a>
    </div>
@endsection

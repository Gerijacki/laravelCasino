@extends('auth.layouts')

@section('content')
<div class="container">
    <h1>Resultado</h1>
    <p>Resultado: {{ $resultMessage }}</p>

    <h3>Tu mano</h3>
    <div>
        {{-- @foreach($playerHand as $card)
            <img src="{{ asset($card['image']) }}" alt="{{ $card['rank'] }} of {{ $card['suit'] }}">
        @endforeach --}}
    </div>
    <p>Total: {{ $playerTotal }}</p>

    <h3>Mano del Dealer</h3>
    <div>
        {{-- @foreach($dealerMoves as $card)
            <img src="{{ asset($card['image']) }}" alt="{{ $card['rank'] }} of {{ $card['suit'] }}">
        @endforeach --}}
    </div>
    <p>Total: {{ $dealerTotal }}</p>

    <p>Saldo Actual: ${{ $user->balance }}</p>
    <a href="{{ route('blackjack.index') }}">Jugar de nuevo</a>
</div>
@endsection

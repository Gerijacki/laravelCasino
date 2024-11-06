@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Blackjack - Game</div>
                <div class="card-body">
                    <h5>Your Balance: ${{ $user->balance }}</h5>
                    <h5>Bet: ${{ $bet }}</h5>
                    <h5>Your Total: {{ $playerTotal }}</h5>
                    <h5>Dealer Total: {{ $dealerTotal }}</h5>

                    <div>
                        <h6>Your Cards:</h6>
                        @foreach ($playerHand as $card)
                            <img src="{{ asset($card['image']) }}" alt="{{ $card['rank'] }} of {{ $card['suit'] }}" width="70" class="me-2">
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <form action="{{ route('blackjack.hit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="player_hand" value="{{ json_encode($playerHand) }}">
                            <input type="hidden" name="deck" value="{{ json_encode($deck) }}">
                            <input type="hidden" name="dealer_hand" value="{{ json_encode($dealerHand) }}">
                            <input type="hidden" name="dealer_total" value="{{ $dealerTotal }}">
                            <input type="hidden" name="bet" value="{{ $bet }}">
                            <button type="submit" class="btn btn-primary">Hit</button>
                        </form>
                    </div>

                    <div class="mt-3">
                        <form action="{{ route('blackjack.dealerTurn') }}" method="POST">
                            @csrf
                            <input type="hidden" name="dealer_hand" value="{{ json_encode($dealerHand) }}">
                            <input type="hidden" name="deck" value="{{ json_encode($deck) }}">
                            <input type="hidden" name="player_total" value="{{ $playerTotal }}">
                            <input type="hidden" name="bet" value="{{ $bet }}">
                            <button type="submit" class="btn btn-secondary">Dealer's Turn</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

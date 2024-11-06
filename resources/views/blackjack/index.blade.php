@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Blackjack - Start Game</div>
                <div class="card-body">
                    <h5>Your Balance: ${{ $user->balance }}</h5>
                    <form action="{{ route('blackjack.start') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="number" name="bet" class="form-control" placeholder="Enter your bet" required min="1" max="{{ $user->balance }}">
                        </div>
                        <button type="submit" class="btn btn-success">Start Game</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

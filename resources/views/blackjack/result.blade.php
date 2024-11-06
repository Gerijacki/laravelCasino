@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Blackjack - Result</div>
                <div class="card-body">
                    <h5>{{ $message }}</h5>
                    <h5>Your Balance: ${{ $user->balance }}</h5>
                    <a href="{{ route('blackjack.index') }}" class="btn btn-primary">Play Again</a>
                </div>
            </div>
        </div>
    </div>
@endsection

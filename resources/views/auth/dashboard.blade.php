@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        {{ $message }}
                    </div>
                @else
                    <div class="alert alert-success">
                        You are logged in!
                    </div>       
                @endif    

                <div class="d-flex justify-content-around mt-4">
                    {{-- <a href="{{ route('ruleta') }}" class="btn btn-primary">
                        Ruleta
                    </a> --}}
                    <a href="{{ redirect('blackjack') }}" class="btn btn-success">
                        BlackJack
                     </a>
                    {{--<a href="{{ route('otros') }}" class="btn btn-warning">
                        Otros
                    </a> --}}
                </div>
                
            </div>
        </div>
    </div>    
</div>

@endsection

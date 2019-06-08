@extends( 'layouts.app' )

@section('content')

    <div class="main-menu">

        <h3>Your Past Games</h3>
        <ul class="menu-list-wrapper">
            @foreach ( $games as $game )


                <li class="menu-list-item">

                    <a href="/games/{{$game->id}}">{{ __( 'Game ' . $game->id ) }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

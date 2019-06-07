@extends( 'layouts.app' )

@section('content')

    <tic-tac-toe
        :playback="{{ $game->states }}"
        :game="{{ $game }}"
    ></tic-tac-toe>
@endsection

@extends( 'layouts.app' )

@section('content')

    <div class="main-menu">

        <h3>Main Menu</h3>
        <ul class="menu-list-wrapper">

            <li class="menu-list-item">

                <a href="{{ route( 'play.games.index' ) }}">{{ __( 'Play AI' ) }}</a>
            </li>
            <li class="menu-list-item">

                <a href="{{ route( 'play.games.startLive' ) }}">{{ __( 'Start Online Game' ) }}</a>
            </li>
            <li class="menu-list-item">

                <a href="{{ route( 'play.games.seeLive' ) }}">{{ __( 'Spectate Live' ) }}</a>
            </li>
            <li class="menu-list-item">

                <a href="{{ route( 'play.games.seePast' ) }}">{{ __( 'See Past Games' ) }}</a>
            </li>
        </ul>
    </div>
@endsection


@extends('layouts.front-layout')
@section('title', 'HABB | '.$model->tournament->name)

@section('content')

    @if ($model->banners_count > 0)
        @include('front.home._banners-slider')
    @endif

    <div class="container">

        <div class="mt-5">
            <h1 class="display-4 ">{{ $model->tournament->name }}</h1>
        </div>

        <div class="row">
            <div class="col-md-8">
                {!! $model->tournament->public_description  !!}

                <div class="mt-1">
                    @include('shared._hashtags', ['hashtags' => $model->tournament->getHashtagsAsArray()])
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">

                    <div class="card-body">

                        <div class="card-title">
                            <h4>Связанные новости</h4>
                        </div>

                        @foreach($model->topNews as $topPost)

                            <div class="mt-2">
                                <a class="card-link habb-post-link" href="{{ action('HomeController@openPost', ['id' => $topPost->id]) }}">#{{ $topPost->title }}</a>
                            </div>

                        @endforeach

                        <div class="mt-3">
                            <a class="btn btn-link" href="{{ action('HomeController@news') }}">Все новости</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            @if($model->showRegisterForTournamentButton)
                <a href="{{ action('RegisterFormController@teamRegisterForTournament', ['t' => $model->tournament->id]) }}"
                   class="btn btn-primary btn-block btn-lg">Участвовать в турнире</a>

            @elseif($model->showRegisterForEventButton)

                <!--p>Регистрация команд на участие в турнире закрыта {{ $model->registrationDeadlineString }}, однако вы можете принять участие как гость на ивенте</p-->
                <div>
                    <a href="{{ action('RegisterFormController@registerAsGuestForTournamentForm', ['tournamentId' => $model->tournament->id,
                    'sharedByHabbId' => $model->sharedByHabbId]) }}"
                       class="btn btn-info btn-block btn-lg">Участвовать в ивенте</a>
                </div>
            @else
                <p>
                    К сожалению, турнир прошел {{ $model->eventDateString }}. Следите за нашими новостями, чтобы не пропустить новые мероприятия.
                </p>

            @endif

        </div>


    </div>

@endsection

@section('styles')
    <style>
        {!! \App\Helpers\HtmlHelpers::getStylesForBannerSlider($model) !!}
    </style>
@endsection
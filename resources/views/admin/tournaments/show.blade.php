
@extends('layouts.admin-layout')
@section('title', 'Информация о турнире')

@section('content')
    <div class="container">
        <div class="mt-1">
            <h1 class="mt-1">Турнир {{ $instance->name }} [ID {{ $instance->id }}]</h1>
            <p class="text-muted">Создание: {{ $instance->CreatedAt() }}. Обновление: {{ $instance->UpdatedAt() }}</p>
        </div>

        <div class="row">
            <div class="col-sm-8">
                {!! $instance->public_description !!}

                <div class="mt-1">
                    @include('shared._hashtags', ['hashtags' => $instance->getHashtagsAsArray()])
                </div>



            </div>
            <div class="col-sm-4">
                <dl>
                    <dt>Дата события</dt>
                    <dd>{{ $instance->getEventDate() }}</dd>

                    <dt>Комментарий</dt>
                    <dd>{{ $instance->comment ?? 'Без комментарий' }}</dd>
                </dl>

                <div class="mt-1">
                    @if($instance->attached_to_nav)
                        <strong>Закреплен в панели навигации</strong>
                    @else
                        <span>Не закрепляется в панели навигации</span>
                    @endif
                </div>



            </div>
        </div>

        <hr>
        <div class="mt-2">
            {{ link_to_action('TournamentController@index', 'В список', [], ['class' => 'btn btn-light']) }}
            <div class="float-sm-right">

                {{ link_to_action('HomeController@openTournament', 'Показать на фронте', ['id' => $instance->id], ['class' => 'btn btn-primary']) }}
                {{ link_to_action('TournamentController@edit', 'Редактировать', ['id' => $instance->id], ['class' => 'btn btn-outline-primary']) }}
                <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteDialog">Удалить</button>
            </div>
        </div>

        <div class="mt-3">

            <div class="h5">Участники турнира</div>

            <table class="table table-striped mt-3">

                <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Капитан</th>
                    <th>Телефон капитана</th>
                    <th>VK капитана</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0; $i < count($participants); $i++)

                    @php($captain = $participants[$i]->captain)

                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $participants[$i]->id }}</td>
                        <td><a href="{{ action('TeamController@show', ['id' => $participants[$i]->id]) }}">{{ $participants[$i]->name }}</a></td>
                        <td><a href="{{ action('GamerController@show', ['id' => $captain->id]) }}">{{ $captain->getFullName() }}</a></td>
                        <td>{{ $captain->phone }}</td>
                        <td><a href="{{ $captain->vk_page }}" target="_blank" title="Открыть в новой вкладке">{{ $captain->vk_page }}</a></td>
                    </tr>
                @endfor
                </tbody>
            </table>

        </div>

    </div>



    <div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-labelledby="deleteDialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Удаление объекта</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' =>'delete', 'action' => ['TournamentController@destroy', $instance->id]]) !!}
                <div class="modal-body">
                    Вы уверены, что хотите удалить запись о турнире #{{ $instance->id }}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-outline-danger">Удалить</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection
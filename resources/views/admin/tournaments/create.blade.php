
@extends('layouts.admin-layout')
@section('title', 'Создание турнира')

@section('content')
    <div class="container">
        <h1 class="mt-1">Создание турнира</h1>
        {!! Form::open(array('action' => array('TournamentController@store'))) !!}
            @include('admin.tournaments.form')
        {!! Form::close() !!}
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('scripts/tournamentHelpers.js') }}"></script>
    <script src="{{ asset('thirdparty/select2/select2.min.js') }}"></script>
    <script type="text/javascript">

        $(".select2-single").select2({
            placeholder: "Выберите участников",
        });
        $(function(){
            $('#form').submit(function(){
                $("#submit-btn").prop('disabled',true);
            });
            habb.tournamentHelpers.registerListeners();
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('thirdparty/select2/select2.min.css') }}">
@endsection
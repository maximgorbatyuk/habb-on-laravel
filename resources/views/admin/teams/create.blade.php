
@extends('layouts.admin-layout')
@section('title', 'Создание записи')

@section('content')
    <div class="container">
        <h1 class="mt-1">Создание команды</h1>
        {!! Form::open(array('action' => array('TeamController@store'))) !!}
        @include('admin/teams/form')
        {!! Form::close() !!}
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('thirdparty/select2/js/select2.js') }}"></script>
    <script type="text/javascript">

        $(".select2-single").select2({
            placeholder: "Выберите игрока",
        });

        $('#form').submit(function(){
            $("#submit-btn").prop('disabled',true);
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('thirdparty/select2/css/select2.css') }}">
@endsection
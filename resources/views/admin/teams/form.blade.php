
<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-block">
                <div class="card-text">

                    <input type="hidden" name="id" value="{{old('id')}}" >
                    <div class="form-group row">
                        {{ Form::label('name', 'Название:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{ Form::text('name', old('name'),
                                array('class' => 'form-control', 'required', 'maxlength' => '100', 'placeholder' => 'Введите название команды')) }}
                            @if ($errors->has('name'))
                                <span class="help-block text-danger">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span><br>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="city">Город</label>
                        <div class="col-sm-9">
                            @renderCitiesSelect(old('city'), true)
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ Form::label('comment', 'Комментарий', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{ Form::textarea('comment', old('comment'),
                                array('class' => 'form-control', 'maxlength' => '250', 'placeholder' => 'Максимум 250 символов')) }}
                            @if ($errors->has('comment'))
                                <span class="help-block text-danger">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span><br>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="col-sm-6">

        <div class="card">
            <div class="card-block">
                <p class="card-text">

                <div class="form-group">
                    {{ Form::select('gamers[]', $gamerOptionList, old('gamers'),
                        ['class'=>'form-control select2-multiple', 'id' => 'gamers', 'multiple' => true, 'required' => true]) }}
                </div>

                </p>
            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <button type="submit" id="submit-btn" class="btn btn-primary">Сохранить</button>

</div>
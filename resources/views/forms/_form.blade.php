<div class="form-group">
    {!! Form::label('name','Form Name',['id'=>'','class'=>'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::text('name',isset($form->name) ? $form->name:null,['id'=>'','class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('type','Form Type',['id'=>'','class'=>'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::select('type',['enumerator' => 'Enumerator', 'spotchecker' => 'Spot Checker'],isset($form->type) ? $form->type:null,['id'=>'','class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('enumerator_form','Enumerator Form',['id'=>'','class'=>'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::select('enumerator_form', $forms, null,['id'=>'','class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('descriptions','Descriptions',['id'=>'','class'=>'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::text('descriptions',isset($form->descriptions) ? $form->descriptions:null,['id'=>'','class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
        {!! Form::label('no_of_answers','Answers per question',['id'=>'','class'=>'control-label col-md-4']) !!}
    <div class="col-md-6">
        {!! Form::text('no_of_answers',isset($form->no_of_answers) ? $form->no_of_answers:null,['id'=>'','class'=>'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('p_group', 'Participants Group: ', ['class' => 'control-label col-md-4']) !!}
    <div class="col-md-6">
    {!! Form::select('p_group', isset($p_group) ? $p_group:['none' => 'None'], ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('start_date', 'Start Date: ', ['class' => 'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::input('date', 'start_date', isset($form->start_date) ? $form->start_date:date('d-m-Y'), ['class' => 'form-control datepicker']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('end_date', 'End Date: ', ['class' => 'control-label col-md-4 ']) !!}
    <div class="col-md-6">
        {!! Form::input('date', 'end_date', isset($form->end_date) ? $form->end_date:date('d-m-Y'), ['class' => 'form-control datepicker']) !!}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-2 col-md-offset-4">
        {!! Form::submit($submitButton, ['class'=>'btn btn-primary form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::close() !!}
</div>
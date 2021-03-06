@extends('adminlte')

@section('content')

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Questions List
				<small></small>
			</h1>
			<!--ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active">Here</li>
            </ol-->
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Questions List</h3>
						</div>
						<!-- /.box-header -->
						<div class="box-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
						{!! Form::open(['url' => 'forms', 'class' => 'form-horizontal']) !!}

						{!! Form::close() !!}
						<table id="sortable-table" class="table">
							<span id="message"></span>
							<thead>

							<th>No.</th>
							<th>Question</th>
							@permission('edit.form')
							<th>Edit</th>
							<th>Delete</th>
							@endpermission
							</thead>
							<tbody>
							@role('admin')
							@foreach ($questions as $k => $question )
								@if( $question->q_type == 'single' )
									<tr id="listid-{{ $question->id }}">
										<td>{{ $question->question_number }}</td>
											<td>{{ $question->question }}
											<div class="col-xs-offset-1">
													@if($question->input_type == 'radio')
														@foreach($question->answers as $answer_k => $answer_v)
															@if(!empty($answer_v))
															{!! Form::radio($question->id.'-'.$answer_k,$answer_k ) !!} {{ $answer_v['text'] }}
															@endif
														@endforeach
																<!--{!! Form::input('date', 'dob', date('d-m-Y'), ['class' => 'form-control']) !!} -->
													@endif
													@if($question->input_type == 'choice')
														@foreach($question->answers as $answer_k => $answer_v)
															@if(!empty($answer_v))
															{!! Form::checkbox($question->id.'-'.$answer_k, $answer_k,false) !!} {{ $answer_v['text'] }}
															@endif
														@endforeach
													@endif
													@if($question->input_type == 'select')
														<div class="">
															{!! Form::select($question->id,array_filter($question->answers), ['class' => 'form-control']) !!}
														</div>
													@endif
													@if($question->input_type == 'text')
														<div class="">
															{!! Form::text($question->id,null, ['class' => 'form-control']) !!}
														</div>
													@endif
													@if($question->input_type == 'textarea')
														<div class="">
															{!! Form::textarea($question->id,null, ['class' => 'form-control']) !!}
														</div>
													@endif
											</div>
											</td>
										@permission('edit.form')
										<td><a href={{ url("/forms/question/".$question->id ) }}>Edit</a></td>
										<td><a href={{ url("/forms/question/".$question->id."/delete" ) }}>Delete</a></td>
										@endpermission
									</tr>

								@elseif( $question->q_type == 'main' || $question->q_type == 'spotchecker')
									<tr id="listid-{{ $question->id }}">
										<td>{{ $question->question_number }}</td>
										<td>{{ $question->question }}
											<div class="col-md-offset-1">
												@foreach($question->get_children as $children)
													<p>{{ $children->question }}
														@permission('edit.form')
														<a href={{ url("/forms/question/".$children->id ) }}>Edit</a>
														<a href={{ url("/forms/question/".$children->id."/delete" ) }}>Delete</a>
														@endpermission
													</p>

															@if($children->input_type == 'radio')
																<div class="">
																	@foreach($children->answers as $answer_k => $answer_v)
																		@if(!empty($answer_v))
																		{!! Form::radio($children->id.'-'.$answer_k, $answer_k ) !!} {{ $answer_v['text'] }}
																		@endif
																	@endforeach
																</div>			<!--{!! Form::input('date', 'dob', date('d-m-Y'), ['class' => 'form-control']) !!} -->
															@endif
															@if($children->input_type == 'choice')
																<div class="">
																	@foreach($children->answers as $answer_k => $answer_v)
																		@if(!empty($answer_v))
																		{!! Form::checkbox($children->id.'-'.$answer_k, $answer_k,false) !!} {{ $answer_v['text'] }}
																		@endif
																	@endforeach
																</div>
															@endif
															@if($children->input_type == 'select')
																<div class="">
																	{!! Form::select($children->id,array_filter($children->answers), true, ['class' => 'form-control']) !!}
																</div>
															@endif
															@if($children->input_type == 'text')
																<div class="">
																	{!! Form::text($children->id,null, ['class' => 'form-control']) !!}
																</div>
															@endif
															@if($children->input_type == 'textarea')
																<div class="">
																	{!! Form::textarea($children->id,null, ['class' => 'form-control']) !!}
																</div>
															@endif

												@endforeach

											</div>
										</td>
										@permission('edit.form')
										<td><a href={{ url("/forms/question/".$question->id ) }}>Edit</a></td>
										<td><a href={{ url("/forms/question/".$question->id."/delete" ) }}>Delete</a></td>
										@endpermission
									</tr>

								@elseif( $question->q_type == 'sub' )
								@endif


							@endforeach

							@endrole
							</tbody>
						</table>

				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	</section>
	<!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	if (typeof ajaxURL === 'undefined') {
		var ajaxURL = "{{ '/'.$form_name_url.'/ajaxsort' }}";
	}
	$(document).ready(function ($) {
		$("#sortable-table tbody").sortable({
			cursor: 'move',
			axis: 'y',
			update: function (event, ui) {
				var order = $(this).sortable("serialize");


				//send ajax request
				$.ajax({
					url    : ajaxURL,
					type   : 'POST',
					data   : order,
					success: function (data) {
						if(data.success){
							$("#message").html('Sorted');
							$("#message").addClass('text-green');
						}else{
							$("#message").html('Something wrong');
							$("#message").addClass('text-red');
						}
					}

				});
			}
		});
	});
</script>
@endsection
{{ Form::open(['method' => 'delete', 'route' => [$model->getScaffoldRoute('destroy'), $model->id]]) }}

	<p>Are you sure you wish to delete: <strong>{{ $model->getNameField(); }}</strong></p>

	{{ Form::token() }}
	
	{{ Form::submit('Delete') }}

{{ Form::close() }}
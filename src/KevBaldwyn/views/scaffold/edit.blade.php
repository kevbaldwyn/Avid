{{ Form::model($model, ['method' => 'put', 'route' => ['admin.' . $model->getTable() . '.update', $model->id]]) }}
	
	<?php 

	$schema = new KevBaldwyn\Avid\Schema\Table($model);
	
	echo $schema->form($ignore, array('customAttributes' => $model->getCustomAttributes()));
	
	?>
	
	{{ Form::token() }}
	
	{{ Form::submit('Save') }}
	
{{ Form::close() }}

{{ Form::model($model, ['method' => 'put', 'route' => [$model->getTable().'.update', $model->id]]) }}
	
	<?php 

	$schema = new KevBaldwyn\Avid\Schema\Table($model);
	
	echo $schema->form($ignore, array('customAttributes' => $model->getCustomAttributes()));
	
	?>
	
	{{ Form::submit('Save') }}
	
{{ Form::close() }}

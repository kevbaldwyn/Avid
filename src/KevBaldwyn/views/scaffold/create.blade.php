{{ Form::model($model, ['method' => 'post', 'route' => [$model->getTable().'.store']]) }}
	
	<?php 

	$schema = new KevBaldwyn\Avid\Schema\Table($model);
	
	echo $schema->form($ignore, array('customAttributes' => $model->getCustomAttributes()));
	
	?>
	
	{{ Form::token() }}
	
	{{ Form::submit('Create') }}
	
{{ Form::close() }}
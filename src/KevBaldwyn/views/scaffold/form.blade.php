{{ Form::model($model, ['method' => 'put', 'route' => ['categories.update', $model->id]]) }}
	
	<?php 

	$schema = new KevBaldwyn\Avid\Schema\Table($model);
	
	echo $schema->form($ignore);
	
	?>
	
	{{ Form::submit('Save') }}
	
{{ Form::close() }}

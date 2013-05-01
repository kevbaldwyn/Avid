{{ Form::model(['route' => [$model->getTable().'.store']]) }}
	
	<?php 

	$schema = new KevBaldwyn\Avid\Schema\Table($model);
	
	echo $schema->form($ignore);
	
	?>
	
	{{ Form::submit('Save') }}
	
{{ Form::close() }}
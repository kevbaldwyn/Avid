<?php
/**
 * provide some useful macros
 */

/**
 * generate an error field for an input
 */
Form::macro('errorField', function($name) {
	return \KevBaldwyn\Avid\Schema\Field::errorField($name);
});
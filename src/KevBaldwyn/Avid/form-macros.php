<?php
/**
 * provide some useful macros
 */

/**
 * generate an error field for an input
 */
Form::macro('error', function($name) {
	return \KevBaldwyn\Avid\Schema\Field::error($name);
});
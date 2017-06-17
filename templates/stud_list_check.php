<?php
$required = ['urgentMessage', 'tbodyContent', 'queries', 'appStatusText'];
foreach ($required as $name) {
	if ( !isset(${$name}) ) {
		throw new \Shinoa\StudentsList\Exceptions\ViewException("Variable is not set: $name");
	}
}

unset($required);
?>
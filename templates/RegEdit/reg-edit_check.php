<?php

if (!isset($caption) || !isset($submitButName) || !isset($mesagge) || !isset($error)
	||
	!isset($defFields['nameVal']) || !isset($defFields['surnameVal'])
	||
	!isset($defFields['mascVal']) || !isset($defFields['femVal'])
	||
	!isset($defFields['groupVal']) || !isset($defFields['emailVal'])
	||
	!isset($defFields['egeSumVal']) || !isset($defFields['birthVal'])
	||
	!isset($defFields['localVal']) || !isset($defFields['nonLocalVal'])
) throw new \Shinoa\StudentsList\Exceptions\ViewException('One or more variables not set');



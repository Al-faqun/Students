<?php
	use Shinoa\StudentsList\Registry;
	use Shinoa\StudentsList\SearchQueryValidator;
	use Shinoa\StudentsList\StudentView;
	use Shinoa\StudentsList\StudentMapper;
	use Shinoa\StudentsList\ErrorHelper;
	use Shinoa\StudentsList\StatusSelector;
	use Shinoa\StudentsList\ErrEvoker;
	include_once 'bootstrap.php';

	//инициализация основных классов
	$registry = Registry::getInstance();
	$registry->init( $root, $configPath );
	try {
		$dataMapper = new StudentMapper($registry->getPDO());
		$view = new StudentView( $registry, appendFilePath([$registry->getRoot(), 'Students', 'templates']) );
		$registry->setDataMapper($dataMapper);
		$registry->setView($view);
		
		//проверка отосланных (или нет) пользователем данных о режиме приложения
		$statusSelector = new StatusSelector();
		if ($statusSelector->dataIn($_POST) !== false) {
			$statusText = $statusSelector->dataIn($_POST);
			$statusSelector->save(StatusSelector::textToCode($statusText));
			header('Location: ' . '/Students/', true, 303);
			exit();
		} elseif ($statusSelector->dataIn($_COOKIE) !== false) {
			$statusText = $statusSelector->dataIn($_COOKIE);
			$registry->setStatus(StatusSelector::textToCode($statusText));
			$registry->setStatusText($statusText);
		} else {
			$registry->statusUseDefault();
			$registry->setStatusText(StatusSelector::codeToText($registry->getStatus()));
		}
		
		//запоминает номер пользователя, если он есть в куки, если нет - ставим 0 по умолчанию
		//значение будет использоваться при записи в лог ошибок
		if (array_key_exists('userid', $_COOKIE) && is_string($_COOKIE['userid'])) {
			$registry->setUserID( (int)$_COOKIE['userid'] );
		} else $registry->setUserID(0);
		
		//проверка отосланных пользователем данных о нужде вызвать ошибку/исключение для проверки работы сайта
		$evoker = new ErrEvoker();
		if ($evoker->isErrorIn($_POST)) {
			header('Location: ' . '/Students/?error', true, 303);
			exit();
		} elseif ($evoker->isExceptionIn($_POST)) {
			header('Location: ' . '/Students/?exception', true, 303);
			exit();
		}
		if ( isset($_GET['error']) ) {
			$evoker->evokeError();
		} elseif ( isset($_GET['exception']) ) {
			$evoker->evokeException();
		}
		
		//проверка посланных (или нет) данных для поиска
		//если данных нет, используются дефолтные значения
		$validator = new SearchQueryValidator($_GET);
		$searchText = $validator->checkSearchText();
		$searchField = $validator->checkSearchField();
		$sortby = $validator->checkSortBy();
		$order = $validator->checkOrder();
		$offset = '';
		$limit = '';
		$validator->checkPage($offset, $limit);
		
		//сохраняем проверенные данные для последующего использовани другими классами
		$registry->setSearchText($searchText);
		$registry->setSearchField($searchField);
		$registry->setSortby($sortby);
		$registry->setOrder($order);
		$registry->setOffset($offset);
		$registry->setLimit($limit);
		
		//отображает страницу на основе собранных выше данных
		$view->render();
		
	//ловим уловимые ошибки и исключения - код расчитан на php 7+
	} catch (\Throwable $e) {
		//класс, заведующий обработкой ошибок
		$errorHelper = new ErrorHelper( appendFilePath([$registry->getRoot(), 'Students', 'templates']) );
		//предпринимаем действия, в зависимости от режима приложения: 'в разработке' или 'в поизводстве'
		switch ($appStatus = $registry->getStatus()) {
			case $registry::APP_IN_DEVELOPMENT:
				$errorHelper->renderExceptionAndExit($e, '/Students');
				break;
				
			case $registry::APP_IN_PRODUCTION:
				$userMes = 'Encountered error, logs are sent to developer. Please, try again later!';
				//форматируем текст для записи в лог-файл
				$text = ErrorHelper::errorToArray($e);
				array_unshift($text, date('d-M-Y H:i:s') . ' ');
				$text[] = 'UserID = ' . $registry->getUserID();
				$logpath = appendFilePath( [$registry->getRoot(), 'Students', 'errors.log'] );
				$errorHelper->addToLog($text, $logpath);
				$errorHelper->renderErrorPageAndExit($userMes, '/Students');
				break;
		}
	}



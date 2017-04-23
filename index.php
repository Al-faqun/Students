<?php
	use \Shinoa\StudentsList\Registry;
	use \Shinoa\StudentsList\SearchQueryValidator;
	use \Shinoa\StudentsList\StudentView;
	use \Shinoa\StudentsList\StudentMapper;
	use \Shinoa\StudentsList\ErrorHelper;

	include_once 'bootstrap.php';

	try {
		//инициализация основных классов
		$root = dirname(__DIR__);
		$registry = Registry::getInstance();
		$registry->init($root, $root . '/Students/ini/config_test.xml');
		$dataMapper = new StudentMapper($registry->getPDO());
		$view       = new StudentView($registry, $registry->getRoot() . '/Students/templates');
		$registry->setDataMapper($dataMapper);
		$registry->setView($view);
		
		//проверка посланных (или нет) данных
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
		
		
	} catch (\Throwable $e) {
		switch ($appStatus = $registry->getStatus()) {
			case $registry::APP_IN_DEVELOPMENT:
				$errorHelper = new ErrorHelper('D:\USR\apache\htdocs\s1.localhost\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
			case $registry::APP_IN_PRODUCTION:
				$errorHelper = new ErrorHelper('D:\USR\apache\htdocs\s1.localhost\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
		}
	} catch (\Exception $e) {
		switch ($appStatus = $registry->getStatus()) {
			case $registry::APP_IN_DEVELOPMENT:
				$errorHelper = new ErrorHelper('D:\USR\apache\htdocs\s1.localhost\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
			case $registry::APP_IN_PRODUCTION:
				$errorHelper = new ErrorHelper('D:\USR\apache\htdocs\s1.localhost\Students\templates');
				$errorHelper->renderExceptionAndExit($e, '');
				break;
		}
	}







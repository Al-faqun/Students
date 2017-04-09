<?php
	use \Shinoa\StudentsList\Registry;
	use \Shinoa\StudentsList\SearchQueryValidator;
	use \Shinoa\StudentsList\StudentsView;
	use \Shinoa\StudentsList\StudentMapper;
	use \Shinoa\StudentsList\ErrorHelper;

	include 'bootstrap.php';

	try {
		$root = dirname(__DIR__);
		$registry = Registry::getInstance();
		$registry->init($root, $root . '/Students/ini/config_test.xml');
		$dataMapper = new StudentMapper($registry->getPDO());
		$registry->setDataMapper($dataMapper);
		$view = new StudentsView($registry, $registry->getRoot() . '/Students/templates');
		$registry->setView($view);
		
		$validator = new SearchQueryValidator($_GET);
		$searchText = $validator->checkSearchText();
		$searchField = $validator->checkSearchField();
		$sortby = $validator->checkSortBy();
		$order = $validator->checkOrder();
		$offset = '';
		$limit = '';
		$validator->checkPage($offset, $limit);
		
		$registry->setSearchText($searchText);
		$registry->setSearchField($searchField);
		$registry->setSortby($sortby);
		$registry->setOrder($order);
		$registry->setOffset($offset);
		$registry->setLimit($limit);
		
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







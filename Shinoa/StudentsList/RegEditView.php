<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

class RegEditView extends CommonView
{
	/**
	 * RegEditView constructor.
	 * @param Registry $registry
	 * @param string $templatesDir
	 */
	function __construct(Registry $registry, $templatesDir)
	{
		parent::__construct($registry, $templatesDir);
	}
	
	/**
	 * Fills array for output in html page, containing data from Student object.
	 * @param Student|bool $student If FALSE instead of object, then array fields are filled with empty strings.
	 * @param array $out (by ref) Is filled with strings, keyed to special names like 'egeSumVal'.
	 * @param bool $makeEmpty If TRUE, out array is filled with empty strings.
	 */
	private function setFormDefaultValues($student, &$out, $makeEmpty = true)
	{
		$out = array();
		if ( $makeEmpty || ($student === false) ) {
			$out['nameVal']   = '';   $out['surnameVal']  = '';
			$out['groupVal']  = '';   $out['emailVal']    = '';
			$out['egeSumVal'] = '';   $out['birthVal']    = '';
			$out['mascVal']   = '';   $out['femVal']     = '';
			$out['localVal']  = '';   $out['nonLocalVal'] = '';
		} else {
			$out['nameVal']    = $student->getName();
			$out['surnameVal'] = $student->getSurname();
			$out['groupVal']   = $student->getGroupNum();
			$out['emailVal']   = $student->getEmail();
			$out['egeSumVal']  = $student->getEgeSum();
			$out['birthVal']   = $student->getYearOfBirth();
			
			switch ( $student->getSex() ) {
				case 'М':
					$out['mascVal'] = 'selected';
					$out['femVal'] = '';
					break;
				case 'Ж':
					$out['mascVal'] = '';
					$out['femVal'] = 'selected';
					break;
				default:
					$out['mascVal'] = '';
					$out['femVal'] = '';
			}
			switch ( $student->getLocation() ) {
				case 'Местный':
					$out['localVal'] = 'selected';
					$out['nonLocalVal'] = '';
					break;
				case 'Иногородний':
					$out['localVal'] = '';
					$out['nonLocalVal'] = 'selected';
					break;
				default:
					$out['localVal'] = '';
					$out['nonLocalVal'] = 'selected';
					break;
			}
		}
	}
	
	/**
	 * Loads all values and preferences for a template, then loads the template into string.
	 * @todo Yeah, I know its nasty code, with no standard and reusability. Gotcha do smth with that.
	 * @return string
	 * @throws ViewException
	 */
	public function output()
	{
		ob_start();
		
		//связь с базой данных
		$this->dataMapper = $this->registry->getDataMapper();
		
		//заголок тела страницы и названия всяких кнопочек
		$caption = ($this->registry->isLogged()) ? 'Обновить данные' : 'Регистрация';
		$submitButName = 'Отправить';
		
		//поля таблицы, которые отображаются как "значения по умолчанию" -
		// например, текущие значения профиля, которые пользователь может изменить
		$defFields = [];
		//сообщения об ошибках и прочие уведомления пользователя
		$error = $this->mesToHTML( $this->registry->getErrors() );
		$mesagge = $this->mesToHTML( $this->registry->getMessages() );
		
		//если пользователь уже добавлял свои данные и авторизирован
		if ( $this->registry->isLogged() ) {
			//если пользователь отредактировал данные с ошибками -
			// загрузить отосланные им неправильные данные, чтобы он их исправил
			if (!empty($error)) {
				$student = $this->registry->getStudentData();
			} else {
				//если нет ошибок - загружаем данные его профиля в "дефолтные значения" таблицы, доступные для редактирования
				$student = $this->registry->getCurrentStudentFromDB();
			}
			//полученные тем или путём данные профиля - форматируем для передачи в шаблон
			$this->setFormDefaultValues($student, $defFields, false);
		//если пользователь ещё не добавлял свои данные
		} elseif ($this->registry->isLogged() === false) {
			//если пользователь отослал данные на регистрацию с ошибками
			if (!empty($error)) {
				$student = $this->registry->getStudentData();
				$this->setFormDefaultValues($student, $defFields, false);
			} else {
				//если пользователь ещё ничего не вводил, заполняем таблицу пустыми строками
				$this->setFormDefaultValues(null, $defFields, true);
			}
			
		}
		//поля нуждаются в защите
		self::escAll($defFields);
		
		//вызывает шаблон, который съест выше подготовленные параметры
		//обратите внимание! в начале шаблона прописаны все необходимые для его работы перменные.
		// Если хоть одна из них не установлена, приложение сваливается с исключением!
		$filepath = $this->templatesDir . '/tpl_reg-edit.php';
		if (file_exists($filepath)) {
			include $filepath;
		} else throw new ViewException('File doesnt exist.');
		
		return ob_get_clean();
		
	}
	
}
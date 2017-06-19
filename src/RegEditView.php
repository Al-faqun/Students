<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\ViewException;

class RegEditView extends CommonView
{
	/**
	 * RegEditView constructor.
     * @param string $templatesDir
     */
    function __construct($templatesDir)
    {
        parent::__construct($templatesDir);
        $this->requiredFields =
            ['student_data', 'is_logged', 'errors', 'messages'];
	    $loader = new \Twig_Loader_Filesystem($templatesDir);
	    $this->twig = new \Twig_Environment($loader, array(
		    'cache' => appendFilePath([$templatesDir, 'cache']),
		    'auto_reload' => false,
		    'autoescape' => 'html'
	    ));
    }
	
    /**
     * Loads all values and preferences for a template, then loads the template into string.
     * @var $params array Link to the params array, from which are retrieved all the data.
     * @return string html page
     * @throws ViewException
     */
	public function output($params)
	{
		ob_start();

		$isLogged    = $params['is_logged'];
		$studentData = $params['student_data'];
		//сообщения об ошибках и прочие уведомления пользователя
		$errors   =  $params['errors'];
		$mesagges =  $params['messages'];
		
		//заголок тела страницы и названия всяких кнопочек
		$caption = $isLogged  ? 'Обновить данные' : 'Регистрация';
		$submitButName = 'Отправить';
		//поля таблицы, которые отображаются как "значения по умолчанию"
		//например, текущие значения профиля, которые пользователь может изменить
		$defFields = [];
		$this->setFormDefaultValues($studentData, $defFields);
		//вызывает шаблон, который съест выше подготовленные параметры
		$template = $this->twig->load( appendFilePath(['RegEdit', 'reg-edit.html.twig']) );
		echo $template->render(array(
			'caption' => $caption,
			'errors' => $errors,
			'messages' => $mesagges,
			'defFields' => $defFields,
			'submitButName' => $submitButName
		));
		return ob_get_clean();
		
	}
	
	/**
	 * Fills array for output in html page, containing data from Student object.
	 * @param Student|bool $student If NULL instead of object, then array fields are filled with empty strings.
	 * @param array $out (by ref) Is filled with strings, keyed to special names like 'egeSumVal'.
	 */
	private function setFormDefaultValues($student, &$out)
	{
		$out = array();
		if ( ($student === false) ) {
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
}
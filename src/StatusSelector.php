<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\StudentException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;

class StatusSelector
{
	const APP_IN_DEVELOPMENT = 0;
	const APP_IN_PRODUCTION  = 1;
	public static $key = 'appStatus';
	private static $const = array('APP_IN_DEVELOPMENT', 'APP_IN_PRODUCTION');
	private static $texts = array('In development', 'In production');
	
	function __construct()
	{
	}
	
	function checkCode($value)
	{
		switch ($value) {
			case '0':
				$result = 0;
				break;
			case '1':
				$result = 1;
				break;
			default:
				$result = false;
				break;
		}
		return $result;
	}
	
	function checkText($value)
	{
		$code = $this->checkCode($value);
		if ( $code !== false ) {
			$text = self::codeToText($code);
		} else $text = false;
		return $text;
	}
	
	function getCode($input)
	{
		if (array_key_exists(self::$key, $input)) {
			switch ($input[self::$key]) {
				case '0':
					$result = 0;
					break;
				case '1':
					$result = 1;
					break;
				default:
					$result = false;
					break;
			}
		} else $result = false;
		return $result;
	}
	
	function getText($input)
	{
		$code = $this->getCode($input);
		if ( $code !== false ) {
			$text = self::codeToText($code);
		} else $text = false;
		return $text;
	}
	
	function save($value, Request $request, Response $response)
	{
		$cookie = FigRequestCookies::get($request, self::$key);
		if ( ($cookie->getValue() === null) || ($cookie->getValue() !== $value) ) {
			$response = FigResponseCookies::set($response, SetCookie::create(self::$key)
				->withValue($value)
				->withExpires(time() + 60 * 60 * 24 * 360)
				->withPath('/')
				->withDomain('.' . $request->getUri()->getHost())
				->withSecure(false)
				->withHttpOnly(true)
			);
		}
		return $response;
	}
	
	static function getDefaultCode($statusFromConfig)
	{
		if (isset($statusFromConfig)) {
			switch ($statusFromConfig) {
				case 'APP_IN_DEVELOPMENT':
					$status = self::APP_IN_DEVELOPMENT;
					break;
				case 'APP_IN_PRODUCTION':
					$status = self::APP_IN_PRODUCTION;
					break;
				default:
					throw new StudentException('App status is not properly loaded');
			}
		} else throw new StudentException('App status is not properly loaded');
		return $status;
	}
	
	function useDefaultText($statusFromConfig)
	{
		return self::codeToText( self::getDefaultCode($statusFromConfig) );
	}
	
	public static function textToCode($statusText)
	{
		switch ($statusText) {
			case self::$texts[0]:
				$result = 0;
				break;
			case self::$texts[1]:
				$result = 1;
				break;
			default:
				$result = false;
				break;
		}
		return $result;
	}
	
	public static function codeToText($statusCode)
	{
		switch ($statusCode) {
			case 0:
				$result = self::$texts[0];
				break;
			case 1:
				$result = self::$texts[1];
				break;
			default:
				$result = false;
				break;
		}
		return $result;
	}
}
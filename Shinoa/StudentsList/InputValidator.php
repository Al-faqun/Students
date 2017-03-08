<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exception\StudentException;

class InputValidator
{
    private $whitelists = array();
    private $inputKeys = array('GET', 'POST');
    private $validatedKeys = array();
    /**
     * Проверяет, содержит ли строка одни цифры.
     * Если переданы один или два дополнительных параметров, происходит сравнение с ними.
     * @param string $inputString
     * @param int $firstNum Если задано только это число, то проверяемое значение сравнивается с ним,
     * если оно меньше его или равно, возвращается true, иначе false.
     * @param int $secNum Если заданы оба дополнительных параметра,
     * то проверяемое значение должно быть в интервале между ними (включительно),
     * в случае успеха возвращается true, иначе false.
     * @throws \UnexpectedValueException Value in this function must be int.
     * @throws \LogicException Unexpected logical branching.
     * @return bool
     */
    public function isInteger($inputString, $firstNum = 0, $secNum = 0)
    {
        if ( !is_int($firstNum ) || (!is_int($secNum )) ) {
            throw new \UnexpectedValueException('Value in this function must be int. ');
        }

        $result = false;
        //если получили строчку, и строчка содержит одни числа
        if ( is_string($inputString) && ctype_digit($inputString) === true ) {
            //превращаем строку в целое число
            $inputNum = (int)$inputString;
            //если опциональные параметры не использованы
            if ($firstNum === 0 && $secNum === 0) {
                $result = true;
            }

            elseif ( $firstNum !== 0 && $secNum === 0 ) {
                //если задействован первый параметр
                //тестируемое число должно быть меньше или равно параметру
                if ( $inputNum <= $firstNum ) {
                    $result = true;
                } else $result = false;
            }

            elseif ( $firstNum !== 0 && $secNum !== 0 ) {
                //если два параметра использованы,
                //тестируемое число должно входить в их диапазон
                if ( ($inputNum >= $firstNum) && ($inputNum <= $secNum) ) {
                    $result = true;
                } else $result = false;
            }

            else throw new \LogicException('Unexpected logical branching. ');
        } else $result = false;

        return $result;
    }

    public function addWhiteList($key, $whitelist)
    {
        if (!is_string($key) || !is_array($whitelist)) {
            throw new \UnexpectedValueException('Unexpected parameter value');
        } else {
            $this->whitelists[$key] = $whitelist;
        }
    }

    public function addInputKey($key, $arrayName)
    {
        if (is_string($key) && (strtoupper($arrayName) === 'GET')) {
            $this->inputKeys['GET'][] = $key;
        }

        if (is_string($key) && (strtoupper($arrayName) === 'POST')) {
            $this->inputKeys['POST'][] = $key;
        }
    }

    public function validateAgainstWhitelistAll()
    {
        $inputArray = array();
        foreach ($this->inputKeys['GET'] as $inputKey) {
            if (array_key_exists($inputKey, $_GET) && is_string($_GET[$inputKey])) {
                foreach ($this->whitelists[$inputKey] as $whiteValue) {
                    if ($inputArray[$inputKey] === $whiteValue) {
                        $this->validatedKeys[$inputKey] = $inputArray[$inputKey];
                    }
                }
            }
        }
        /*if (!is_string($inputKey)
            || ((strtoupper($inputArrayName) !== 'GET') && (strtoupper($inputArrayName) !== 'POST'))
            || !is_array($whitelist)) {
            throw new \UnexpectedValueException('Unexpected parameter value');
        }

        switch (true) {
            case (strtoupper($inputArrayName) === 'GET'):
                $inputArray = $_GET;
                break;
            case (strtoupper($inputArrayName) === 'POST'):
                $inputArray = $_POST;
                break;
        }

        if (array_key_exists($inputKey, $inputArray) && is_string($inputArray[$inputKey])) {
            foreach ($whitelist as $whiteValue) {
                if ($inputArray[$inputKey] === $whiteValue) {
                    $this->validatedKeys[$inputKey] = $inputArray[$inputKey];
                }
            }
        }*/
        /*if (array_key_exists('sort_by', $_GET) && is_string($_GET['sort_by'])) {
            switch ($_GET['sort_by']) {
                case 'name':
                case 'surname':
                case 'sex':
                case 'group_num':
                case 'e-mail':
                case 'ege_sum':
                case 'year_of_birth':
                case 'location':
                    $this->validatedKeys['sort_by'] = $_GET['sort_by'];
                    break;
                default: break;
            }
        } */

        return $this->validatedKeys;
    }
}
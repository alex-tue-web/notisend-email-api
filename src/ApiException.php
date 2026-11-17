<?php
namespace Notisend\EmailApi;

/**
 * Исключение, возникающее при ошибке API или сети.
 */
class ApiException extends \Exception
{
    /**
     * @var array Массив ошибок из тела ответа (элементы с ключами code/detail)
     */
    private $errors;

    /**
     * @param string $message
     * @param int    $code
     * @param array  $errors
     */
    public function __construct($message, $code = 0, array $errors = array())
    {
        parent::__construct($message, $code);

        $this->errors = $errors;
    }

    /**
     * Возвращает структурированные ошибки API.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

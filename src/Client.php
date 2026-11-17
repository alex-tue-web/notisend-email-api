<?php
namespace Notisend\EmailApi;

/**
 * HTTP-клиент Email API Notisend.
 *
 * Реализован на встроенном cURL, внешних зависимостей нет.
 * Совместим с PHP 5.6+.
 */
class Client
{
    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var int Таймаут соединения и ответа, секунды
     */
    private $timeout;

    /**
     * @param string $apiToken API-токен Notisend
     * @param string $baseUrl  Базовый URL API
     * @param int    $timeout  Таймаут запроса в секундах
     */
    public function __construct($apiToken, $baseUrl = 'https://api.notisend.ru/v1/email', $timeout = 30)
    {
        $this->apiToken = $apiToken;
        $this->baseUrl  = rtrim($baseUrl, '/');
        $this->timeout  = (int) $timeout;
    }

    /**
     * Выполняет запрос к API.
     *
     * @param string     $method HTTP-метод (GET, POST, PATCH, DELETE)
     * @param string     $path   Путь (например, '/messages')
     * @param array|null $data   Ассоциативный массив для JSON-тела
     * @param array|null $files  Массив файлов для multipart/form-data
     * @return array Декодированный JSON-ответ
     * @throws ApiException
     */
    public function request($method, $path, array $data = null, array $files = null)
    {
        // Ключ attachments в теле запроса — сокращённая форма передачи файлов.
        if ($files === null && is_array($data) && isset($data['attachments'])) {
            $files = array('attachments' => $data['attachments']);
            unset($data['attachments']);
        }

        $headers = array(
            'Authorization: Bearer ' . $this->apiToken,
            'Accept: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (is_array($files) && count($files) > 0) {
            // multipart/form-data: Content-Type не задаём — cURL выставит boundary сам.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildMultipartFields($data, $files));
        } else {
            $headers[] = 'Content-Type: application/json';

            if ($data !== null) {
                // Пустой массив кодируем как {} — API ожидает JSON-объект, а не [].
                $payload = count($data) > 0 ? $data : new \stdClass();
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($errno !== 0) {
            throw new ApiException('cURL error: ' . $error, $errno);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $body    = json_decode($response, true);
            $errors  = (is_array($body) && isset($body['errors'])) ? $body['errors'] : array();
            $message = isset($errors[0]['detail']) ? $errors[0]['detail'] : 'Unknown API error';

            throw new ApiException($message, $httpCode, $errors);
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : array();
    }

    /**
     * Ресурс «Баланс».
     *
     * @return Balance
     */
    public function balance()
    {
        return new Balance($this);
    }

    /**
     * Ресурс «Сообщения».
     *
     * @return Messages
     */
    public function messages()
    {
        return new Messages($this);
    }

    /**
     * Ресурс «Шаблоны».
     *
     * @return Templates
     */
    public function templates()
    {
        return new Templates($this);
    }

    /**
     * Ресурс «Списки».
     *
     * @return Lists
     */
    public function lists()
    {
        return new Lists($this);
    }

    /**
     * Ресурс «Рассылки».
     *
     * @return Campaigns
     */
    public function campaigns()
    {
        return new Campaigns($this);
    }

    /**
     * Собирает поля тела multipart/form-data.
     *
     * Скалярные значения передаются как есть, массивы — строкой JSON.
     *
     * @param array|null $data
     * @param array      $files
     * @return array
     */
    private function buildMultipartFields(array $data = null, array $files = array())
    {
        $fields = array();

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $fields[$key] = json_encode($value);
                } elseif (is_bool($value)) {
                    $fields[$key] = $value ? 'true' : 'false';
                } elseif ($value !== null) {
                    $fields[$key] = (string) $value;
                }
            }
        }

        foreach ($files as $field => $file) {
            if (is_array($file)) {
                // Повторяющееся имя поля (attachments[]): cURL отправит несколько частей.
                foreach ($file as $single) {
                    $fields[$field . '[]'] = $this->makeCurlFile($single);
                }
            } else {
                $fields[$field] = $this->makeCurlFile($file);
            }
        }

        return $fields;
    }

    /**
     * Приводит путь к файлу (или готовый CURLFile) к значению для CURLOPT_POSTFIELDS.
     *
     * @param mixed $file
     * @return \CURLFile|string
     * @throws ApiException
     */
    private function makeCurlFile($file)
    {
        if (is_object($file)) {
            return $file;
        }

        if (!is_string($file) || $file === '') {
            throw new ApiException('Attachment must be a non-empty file path or CURLFile instance');
        }

        if (!is_file($file) || !is_readable($file)) {
            throw new ApiException('Attachment is not readable: ' . $file);
        }

        if (class_exists('CURLFile')) {
            return new \CURLFile($file);
        }

        return '@' . $file;
    }
}

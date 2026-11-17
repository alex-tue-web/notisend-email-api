<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Сообщения»: POST /messages, GET /messages/{id}
 */
class Messages
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Отправляет сообщение.
     *
     * Поддерживаемые ключи: from_email, from_name, to, subject, text, html,
     * params, attachments (массив путей к файлам, суммарно ≤ 5 МБ) и др.
     *
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function send(array $data)
    {
        return $this->client->request('POST', '/messages', $data);
    }

    /**
     * Возвращает сообщение по идентификатору.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function get($id)
    {
        return $this->client->request('GET', '/messages/' . $id);
    }
}

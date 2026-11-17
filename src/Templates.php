<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Шаблоны»: /templates
 */
class Templates
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
     * Создаёт шаблон.
     *
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function create(array $data)
    {
        return $this->client->request('POST', '/templates', $data);
    }

    /**
     * Возвращает список шаблонов.
     *
     * @param array $params page_number, page_size
     * @return array
     * @throws ApiException
     */
    public function all(array $params = array())
    {
        return $this->client->request('GET', '/templates?' . http_build_query($params));
    }

    /**
     * Возвращает шаблон по идентификатору.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function get($id)
    {
        return $this->client->request('GET', '/templates/' . $id);
    }

    /**
     * Обновляет шаблон.
     *
     * @param string|int $id
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function update($id, array $data)
    {
        return $this->client->request('PATCH', '/templates/' . $id, $data);
    }

    /**
     * Отправляет сообщение по шаблону.
     *
     * @param string|int $templateId
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function send($templateId, array $data)
    {
        return $this->client->request('POST', '/templates/' . $templateId . '/messages', $data);
    }
}

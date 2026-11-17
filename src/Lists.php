<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Списки»: /lists
 */
class Lists
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
     * Создаёт список.
     *
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function create(array $data)
    {
        return $this->client->request('POST', '/lists', $data);
    }

    /**
     * Возвращает список списков.
     *
     * @param array $params page_number, page_size
     * @return array
     * @throws ApiException
     */
    public function all(array $params = array())
    {
        return $this->client->request('GET', '/lists?' . http_build_query($params));
    }

    /**
     * Возвращает список по идентификатору.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function get($id)
    {
        return $this->client->request('GET', '/lists/' . $id);
    }

    /**
     * Обновляет список.
     *
     * @param string|int $id
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function update($id, array $data)
    {
        return $this->client->request('PATCH', '/lists/' . $id, $data);
    }

    /**
     * Удаляет список.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function delete($id)
    {
        return $this->client->request('DELETE', '/lists/' . $id);
    }

    /**
     * Подресурс «Параметры списка».
     *
     * @return Parameters
     */
    public function parameters()
    {
        return new Parameters($this->client);
    }

    /**
     * Подресурс «Получатели».
     *
     * @return Recipients
     */
    public function recipients()
    {
        return new Recipients($this->client);
    }
}

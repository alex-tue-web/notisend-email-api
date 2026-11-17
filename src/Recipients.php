<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Получатели»: /lists/{list-id}/recipients
 */
class Recipients
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
     * Добавляет получателя в список.
     *
     * @param string|int $listId
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function create($listId, array $data)
    {
        return $this->client->request('POST', '/lists/' . $listId . '/recipients', $data);
    }

    /**
     * Возвращает получателя по идентификатору.
     *
     * @param string|int $listId
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function get($listId, $id)
    {
        return $this->client->request('GET', '/lists/' . $listId . '/recipients/' . $id);
    }

    /**
     * Обновляет получателя.
     *
     * @param string|int $listId
     * @param string|int $id
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function update($listId, $id, array $data)
    {
        return $this->client->request('PATCH', '/lists/' . $listId . '/recipients/' . $id, $data);
    }

    /**
     * Возвращает получателей списка.
     *
     * @param string|int $listId
     * @param array      $params page_number, page_size
     * @return array
     * @throws ApiException
     */
    public function all($listId, array $params = array())
    {
        return $this->client->request('GET', '/lists/' . $listId . '/recipients?' . http_build_query($params));
    }
}

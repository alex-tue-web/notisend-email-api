<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Параметры списка»: /lists/{list-id}/parameters
 */
class Parameters
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
     * Создаёт параметр списка.
     *
     * @param string|int $listId
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function create($listId, array $data)
    {
        return $this->client->request('POST', '/lists/' . $listId . '/parameters', $data);
    }

    /**
     * Возвращает параметры списка.
     *
     * @param string|int $listId
     * @param array      $params page_number, page_size
     * @return array
     * @throws ApiException
     */
    public function all($listId, array $params = array())
    {
        return $this->client->request('GET', '/lists/' . $listId . '/parameters?' . http_build_query($params));
    }

    /**
     * Обновляет параметр списка.
     *
     * @param string|int $listId
     * @param string|int $id
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function update($listId, $id, array $data)
    {
        return $this->client->request('PATCH', '/lists/' . $listId . '/parameters/' . $id, $data);
    }

    /**
     * Удаляет параметр списка.
     *
     * @param string|int $listId
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function delete($listId, $id)
    {
        return $this->client->request('DELETE', '/lists/' . $listId . '/parameters/' . $id);
    }
}

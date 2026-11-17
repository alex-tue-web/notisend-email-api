<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Баланс»: GET /balance
 */
class Balance
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
     * Возвращает текущий баланс аккаунта.
     *
     * @return array
     * @throws ApiException
     */
    public function get()
    {
        return $this->client->request('GET', '/balance');
    }
}

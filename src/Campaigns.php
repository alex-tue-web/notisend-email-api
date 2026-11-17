<?php
namespace Notisend\EmailApi;

/**
 * Ресурс «Рассылки»: /campaigns
 *
 * Методы повторяют действия личного кабинета: создание черновика, немедленная
 * отправка, отправка по расписанию и отмена.
 *
 * @see https://notisend.ru/dev/email/api/
 */
class Campaigns
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
     * Создаёт рассылку в статусе «Черновик» (draft).
     *
     * Обязательные ключи: from_email, subject, html. Дополнительно: from_name,
     * text, segment_id либо lists (массив вида array(array('id' => 1))),
     * attachments — массив путей к файлам (запрос уйдёт как multipart/form-data).
     *
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function create(array $data)
    {
        return $this->client->request('POST', '/campaigns', $data);
    }

    /**
     * Возвращает список рассылок аккаунта.
     *
     * @param array $params statistic (bool), page_number, page_size
     * @return array
     * @throws ApiException
     */
    public function all(array $params = array())
    {
        return $this->client->request('GET', '/campaigns?' . $this->buildQuery($params));
    }

    /**
     * Возвращает рассылку по идентификатору.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function get($id)
    {
        return $this->client->request('GET', '/campaigns/' . $id);
    }

    /**
     * Немедленно отправляет ранее созданную рассылку.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function deliver($id)
    {
        return $this->client->request('PATCH', '/campaigns/' . $id . '/deliver');
    }

    /**
     * Планирует отправку рассылки на заданное время.
     *
     * Ключи: start_at («30.10.2022 13:00») и time_zone («Moscow»). Оба необязательны:
     * при отсутствии сервер использует значения по умолчанию.
     *
     * @param string|int $id
     * @param array      $data
     * @return array
     * @throws ApiException
     */
    public function schedule($id, array $data = array())
    {
        return $this->client->request('PATCH', '/campaigns/' . $id . '/schedule', $data);
    }

    /**
     * Отменяет отправку рассылки.
     *
     * Рассылка на модерации (pending) или запланированная (delayed) возвращается
     * в черновик (draft); отправляющаяся (sending) останавливается (stopping,
     * затем stopped). Черновик, завершённую, остановленную или отменённую
     * рассылку отменить нельзя — API вернёт 409 и ApiException.
     *
     * @param string|int $id
     * @return array
     * @throws ApiException
     */
    public function cancel($id)
    {
        return $this->client->request('PATCH', '/campaigns/' . $id . '/cancel');
    }

    /**
     * Собирает query-строку, приводя булевы значения к 'true'/'false'.
     *
     * http_build_query() превратил бы false в '0', а API ожидает 'false'.
     *
     * @param array $params
     * @return string
     */
    private function buildQuery(array $params)
    {
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $params[$key] = $value ? 'true' : 'false';
            }
        }

        return http_build_query($params);
    }
}

# Notisend Email API

Объектно-ориентированная клиентская библиотека для [Email API Notisend](https://notisend.ru/) на PHP.
Совместима с PHP 5.6+, внешних зависимостей нет — только встроенные расширения `curl` и `json`.

## Требования

- PHP >= 5.6
- Расширение `curl`
- Расширение `json`

## Установка

### Вариант 1. Через VCS-репозиторий

Добавьте репозиторий и зависимость в `composer.json` вашего проекта:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/<VENDOR>/notisend-email-api" }
    ],
    "require": {
        "notisend/email-api": "^1.0"
    }
}
```

Затем выполните:

```bash
composer install
```

### Вариант 2. С Packagist (после публикации пакета)

```bash
composer require notisend/email-api
```

## Обновление

Ограничение `^1.0` — это не фиксация одной версии, а диапазон `>=1.0.0 <2.0.0`.
Composer сам выбирает **наивысший тег** в этом диапазоне, поэтому новые минорные и
патч-релизы (`v1.1.0`, `v1.2.0`, `v1.9.3`) подтягиваются без правок `composer.json`:

```bash
composer update notisend/email-api
```

Для обновления до новой мажорной версии (2.x) измените ограничение вручную:

```bash
composer require "notisend/email-api:^2.0"
```

Что важно знать:

- **Версия живёт в тегах.** Если в репозитории нет тега, Composer не увидит версию —
  `^1.0` не найдёт ничего. Выпускайте тег на каждый релиз (`git tag -a v1.1.0`).
- **`composer install` не обновляет зависимости.** Он ставит версии, зафиксированные
  в `composer.lock`. Чтобы получить новый релиз, нужен именно
  `composer update notisend/email-api`.
- **Обновляйте точечно.** `composer update` без аргументов обновит все зависимости
  проекта и может привести к несовместимостям — указывайте имя пакета.
- **`dev-main` не для production.** Ветка отдаёт самый свежий коммит, включая
  незавершённую работу; для стабильных проектов используйте теги.
- **Composer 2.x требует PHP 7.2.5+.** На PHP 5.6 работает только Composer 1.10.x.

## Быстрый старт

```php
<?php
require 'vendor/autoload.php';

use Notisend\EmailApi\Client;

$client = new Client('YOUR_API_TOKEN');

$result = $client->messages()->send(array(
    'from_email' => 'alice@example.org',
    'from_name'  => 'Alice',
    'to'         => 'bob@example.org',
    'subject'    => 'Hello',
    'text'       => 'Hello, Bob!',
));

print_r($result);
```

## Доступные ресурсы

| Метод | Описание |
|-------|----------|
| `$client->balance()->get()` | Текущий баланс аккаунта (`GET /balance`) |
| `$client->messages()->send($data)` | Отправка сообщения (`POST /messages`) |
| `$client->messages()->get($id)` | Информация о сообщении (`GET /messages/{id}`) |
| `$client->templates()->create($data)` | Создание шаблона (`POST /templates`) |
| `$client->templates()->all($params)` | Список шаблонов с пагинацией (`GET /templates`) |
| `$client->templates()->get($id)` | Шаблон по идентификатору (`GET /templates/{id}`) |
| `$client->templates()->update($id, $data)` | Обновление шаблона (`PATCH /templates/{id}`) |
| `$client->templates()->send($templateId, $data)` | Отправка по шаблону (`POST /templates/{id}/messages`) |
| `$client->lists()->create($data)` | Создание списка (`POST /lists`) |
| `$client->lists()->all($params)` | Список списков с пагинацией (`GET /lists`) |
| `$client->lists()->get($id)` | Список по идентификатору (`GET /lists/{id}`) |
| `$client->lists()->update($id, $data)` | Обновление списка (`PATCH /lists/{id}`) |
| `$client->lists()->delete($id)` | Удаление списка (`DELETE /lists/{id}`) |
| `$client->lists()->parameters()->create($listId, $data)` | Создание параметра списка |
| `$client->lists()->parameters()->all($listId, $params)` | Параметры списка |
| `$client->lists()->parameters()->update($listId, $id, $data)` | Обновление параметра списка |
| `$client->lists()->parameters()->delete($listId, $id)` | Удаление параметра списка |
| `$client->lists()->recipients()->create($listId, $data)` | Добавление получателя в список |
| `$client->lists()->recipients()->all($listId, $params)` | Получатели списка |
| `$client->lists()->recipients()->get($listId, $id)` | Получатель по идентификатору |
| `$client->lists()->recipients()->update($listId, $id, $data)` | Обновление получателя |
| `$client->campaigns()->create($data)` | Создание рассылки-черновика (`POST /campaigns`) |
| `$client->campaigns()->all($params)` | Список рассылок с пагинацией (`GET /campaigns`) |
| `$client->campaigns()->get($id)` | Информация о рассылке (`GET /campaigns/{id}`) |
| `$client->campaigns()->deliver($id)` | Немедленная отправка рассылки (`PATCH /campaigns/{id}/deliver`) |
| `$client->campaigns()->schedule($id, $data)` | Отправка по расписанию (`PATCH /campaigns/{id}/schedule`) |
| `$client->campaigns()->cancel($id)` | Отмена отправки рассылки (`PATCH /campaigns/{id}/cancel`) |

Пагинация задаётся массивом `$params`: `array('page_number' => 1, 'page_size' => 25)`
(`page_size` — максимум 100).

## Обработка ошибок

Любой ответ вне диапазона 2xx, а также сетевая ошибка cURL приводят к исключению
`Notisend\EmailApi\ApiException`. Структурированные ошибки API доступны через `getErrors()`:

```php
<?php
require 'vendor/autoload.php';

use Notisend\EmailApi\Client;
use Notisend\EmailApi\ApiException;

$client = new Client('YOUR_API_TOKEN');

try {
    $result = $client->messages()->send(array(
        'from_email' => 'alice@example.org',
        'to'         => 'bob@example.org',
        'subject'    => 'Hello',
        'text'       => 'Hello, Bob!',
    ));
} catch (ApiException $e) {
    echo 'HTTP code: ' . $e->getCode() . PHP_EOL;
    echo 'Message: '   . $e->getMessage() . PHP_EOL;

    foreach ($e->getErrors() as $error) {
        echo '[' . $error['code'] . '] ' . $error['detail'] . PHP_EOL;
    }
}
```

| HTTP-код | Значение |
|----------|----------|
| 2xx | Успех |
| 400, 401, 402, 404, 409, 415, 422, 429, 500 | Ошибка (тело: `{"errors":[{"code":400,"detail":"..."}]}`) |

## Вложения

Передайте массив путей к файлам в ключе `attachments` — запрос уйдёт как
`multipart/form-data`. Суммарный размер вложений — не более 5 МБ.

```php
<?php
require 'vendor/autoload.php';

use Notisend\EmailApi\Client;

$client = new Client('YOUR_API_TOKEN');

$result = $client->messages()->send(array(
    'from_email'  => 'alice@example.org',
    'from_name'   => 'Alice',
    'to'          => 'bob@example.org',
    'subject'     => 'Report',
    'text'        => 'See attachment.',
    'attachments' => array(
        '/path/to/report.pdf',
        '/path/to/invoice.pdf',
    ),
));
```

## Рассылки

Рассылка создаётся в статусе «Черновик» (`draft`), после чего её можно отправить
немедленно, запланировать или отменить. Обязательные поля при создании: `from_email`,
`subject`, `html`; также нужен `segment_id` либо `lists`.

```php
<?php
require 'vendor/autoload.php';

use Notisend\EmailApi\Client;

$client = new Client('YOUR_API_TOKEN');

// 1. Создание черновика
$campaign = $client->campaigns()->create(array(
    'from_email' => 'hello@example.org',
    'from_name'  => 'Hello',
    'subject'    => 'Hello World',
    'text'       => 'Hello World',
    'html'       => '<h1>Hello World</h1>',
    'lists'      => array(
        array('id' => '1'),
    ),
));

$id = $campaign['id'];

// 2a. Немедленная отправка
$client->campaigns()->deliver($id);

// 2b. Либо отправка по расписанию
$client->campaigns()->schedule($id, array(
    'start_at'  => '30.10.2026 13:00',
    'time_zone' => 'Moscow',
));

// 3. Отмена отправки (pending/delayed -> draft, sending -> stopped)
$client->campaigns()->cancel($id);

// 4. Список рассылок: statistic=false ускоряет выдачу
$list = $client->campaigns()->all(array(
    'statistic'   => false,
    'page_number' => 1,
    'page_size'   => 25,
));

// 5. Информация о рассылке
$one = $client->campaigns()->get($id);
```

Черновик, завершённую, остановленную или отменённую рассылку отменить нельзя —
API вернёт код 409 и `ApiException`.

Рассылки поддерживают вложения так же, как сообщения: добавьте ключ `attachments`
с массивом путей к файлам, и запрос уйдёт как `multipart/form-data`.

```php
$client->campaigns()->create(array(
    'from_email'  => 'hello@example.org',
    'subject'     => 'Mail with attachments',
    'html'        => '<h1>Hello world</h1>',
    'lists'       => array(
        array('id' => '1'),
    ),
    'attachments' => array(
        '/path/to/file1.pdf',
        '/path/to/file2.pdf',
    ),
));
```

## Совместимость

Compatible with PHP 5.6+; no external dependencies beyond cURL and JSON.

## Лицензия

[MIT](LICENSE)

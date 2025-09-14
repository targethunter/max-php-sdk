# MAX PHP SDK

PHP SDK для работы с API мессенджера MAX. Этот пакет предоставляет удобный интерфейс для взаимодействия с MAX Bot API.

## Установка

Установите пакет через Composer:

```bash
composer require targethunter/max-php-sdk
```

## Требования

- PHP 7.4 или выше
- GuzzleHttp 7.5 или выше
- Расширение JSON

## Быстрый старт

### Инициализация клиента

```php
<?php

use TH\MAX\Client\MAXClient;
use TH\MAX\Client\Request\MAXRequest;

// Создаем клиент с вашим access_token
$accessToken = 'ваш_access_token_здесь';
$request = new MAXRequest($accessToken);
$client = new MAXClient($request);

// Теперь вы можете использовать все модули API
```

### Получение информации о боте

```php
// Получить информацию о текущем боте
$bot = $client->bots()->getMe();
echo "Имя бота: " . $bot->name;
echo "Описание: " . $bot->description;
```

## Модули API

SDK разделен на несколько модулей, для удобства использования. Модули реализованы так же, как в официальном API MAX.

### 1. Модуль Bots (Боты)

Управление информацией о боте.

#### Примеры:

```php
// Получить информацию о боте
$bot = $client->bots()->getMe();
echo "ID бота: " . $bot->id;
echo "Имя: " . $bot->name;

// Обновить информацию о боте
$updatedBot = $client->bots()->update(
    first_name: 'Мой',
    last_name: 'Бот',
    name: 'my_bot',
    description: 'Описание моего бота',
    commands: [
        ['command' => 'start', 'description' => 'Запустить бота'],
        ['command' => 'help', 'description' => 'Показать помощь']
    ]
);
```

### 2. Модуль Messages (Сообщения)

Работа с сообщениями.

#### Примеры:

```php
// Отправить текстовое сообщение
$message = $client->messages()->send(
    user_id: 12345,
    text: 'Привет! Это тестовое сообщение от бота.'
);

// Отправить сообщение в чат
$message = $client->messages()->send(
    chat_id: 67890,
    text: 'Сообщение в групповой чат'
);

// Получить список сообщений
$messages = $client->messages()->getAll(
    chat_id: 67890,
    count: 20
);

// Обновить сообщение
$result = $client->messages()->update(
    message_id: 'message_123',
    text: 'Обновленный текст сообщения'
);

// Удалить сообщение
$result = $client->messages()->delete('message_123');

// Получить сообщение по ID
$message = $client->messages()->getById('message_123');
```

### 3. Модуль Chats (Чаты)

Управление чатами и участниками.

#### Примеры:

```php
// Получить список чатов
$chats = $client->chats()->getAll(count: 50);

// Получить чат по ID
$chat = $client->chats()->getById(12345);
echo "Название чата: " . $chat->title;

// Обновить информацию о чате
$updatedChat = $client->chats()->update(
    chat_id: 12345,
    title: 'Новое название чата',
    notify: true
);

// Получить список участников
$members = $client->chats()->getMembers(
    chat_id: 12345,
    count: 20
);

// Добавить участников в чат
$result = $client->chats()->addMembers(
    chat_id: 12345,
    user_ids: [111, 222, 333]
);

// Закрепить сообщение
$result = $client->chats()->pinMessage(
    chat_id: 12345,
    message_id: 'message_123',
    notify: true
);
```

### 4. Модуль Upload (Загрузка файлов)

Загрузка файлов в MAX.

#### Примеры:

```php
// Получить URL для загрузки изображения
$uploadUrl = $client->upload()->getUrl('image');
echo "URL для загрузки: " . $uploadUrl->url;

// Получить URL для загрузки файла
$uploadUrl = $client->upload()->getUrl('file');
```

### 5. Модуль Subscriptions (Подписки)

Управление webhook подписками.

#### Примеры:

```php
// Получить список подписок
$subscriptions = $client->subscriptions()->getAll();

// Создать подписку на webhook
$result = $client->subscriptions()->subscribe(
    url: 'https://your-domain.com/webhook',
    update_types: ['message', 'chat_member'],
    secret: 'your_secret_key'
);

// Удалить подписку
$result = $client->subscriptions()->unsubscribe(
    url: 'https://your-domain.com/webhook'
);

// Получить обновления
$updates = $client->subscriptions()->getUpdates(
    limit: 100,
    timeout: 30
);
```

## Обработка ошибок

SDK автоматически обрабатывает ошибки от API MAX и преобразует их в читаемый формат. Все методы могут выбрасывать исключения `MAXHttpException` при ошибках сети или API.

### Автоматическая обработка ошибок

SDK автоматически:
- Извлекает человекочитаемые сообщения об ошибках из ответов API
- Сохраняет HTTP статус код
- Сохраняет оригинальное исключение Guzzle для отладки

```php
use TH\MAX\Exceptions\MAXHttpException;

try {
    $message = $client->messages()->send(
        user_id: 12345,
        text: 'Тестовое сообщение'
    );
    echo "Сообщение отправлено: " . $message->id;
} catch (MAXHttpException $e) {
    echo "Ошибка API: " . $e->getMessage();
    echo "HTTP код: " . $e->getCode();
    
    // Получить оригинальное исключение Guzzle для отладки
    if ($e->hasOriginalException()) {
        $originalException = $e->getOriginalException();
        // Дополнительная отладочная информация
    }
}
```

### Типы ошибок

SDK обрабатывает различные форматы ошибок от MAX API:
- `message` - основное сообщение об ошибке
- `error.message` - сообщение в объекте error
- `error.description` - описание ошибки
- `description` - альтернативное поле описания

Если ответ не в формате JSON, SDK вернет сырое тело ответа.

## Конфигурация

### Базовый URL API

По умолчанию SDK использует базовый URL API: `https://botapi.max.ru/`

### Кастомный HTTP клиент

Вы можете создать собственный HTTP клиент и передать его в конструктор `MAXRequest`:

```php
use GuzzleHttp\Client;

$customClient = new Client([
    'timeout' => 30,
    'verify' => false, // Отключить проверку SSL (не рекомендуется для продакшена)
]);

$request = new MAXRequest($accessToken, $customClient);
$client = new MAXClient($request);
```

### Кастомизация через наследование

Если вам нужно изменить поведение SDK (например, использовать другой URL API или отключить автоматическую обработку ошибок), вы можете унаследоваться от `MAXRequest` и переопределить нужные методы:

#### Пример кастомизации

```php
use GuzzleHttp\Exception\GuzzleException;
use TH\MAX\Client\Request\MAXRequest;
use TH\MAX\Exceptions\MAXHttpException;

class FullyCustomMAXRequest extends MAXRequest
{
    protected function getURL(string $method): string
    {
        // Кастомный URL с дополнительными параметрами
        $baseUrl = 'https://api.max.ru/v2/';
        return $baseUrl . ltrim($method, '/') . '?version=2.0';
    }

    protected function toMAXHttpException(GuzzleException $e): MAXHttpException
    {
        // Кастомная обработка ошибок
        $code = 500; // Всегда возвращаем 500 для внутренних ошибок
        $msg = 'Произошла ошибка при обращении к API';
        
        return new MAXHttpException($msg, $code, $e);
    }
}
```

## Полный пример использования

```php
<?php

require_once 'vendor/autoload.php';

use TH\MAX\Client\MAXClient;
use TH\MAX\Client\Request\MAXRequest;
use TH\MAX\Exceptions\MAXHttpException;

// Инициализация
$accessToken = 'ваш_access_token';
$request = new MAXRequest($accessToken);
$client = new MAXClient($request);

try {
    // Получить информацию о боте
    $bot = $client->bots()->getMe();
    echo "Бот: " . $bot->name . "\n";
    
    // Получить список чатов
    $chats = $client->chats()->getAll(count: 10);
    echo "Найдено чатов: " . count($chats->chats) . "\n";
    
    // Отправить сообщение в первый чат
    if (!empty($chats->chats)) {
        $firstChat = $chats->chats[0];
        $message = $client->messages()->send(
            chat_id: $firstChat->id,
            text: 'Привет из PHP SDK!'
        );
        echo "Сообщение отправлено с ID: " . $message->id . "\n";
    }
    
} catch (MAXHttpException $e) {
    echo "Ошибка API MAX: " . $e->getMessage() . "\n";
    echo "HTTP код: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage() . "\n";
}
```

## Лицензия

MIT License

## Поддержка

Если у вас есть вопросы или проблемы, создайте issue в репозитории проекта.

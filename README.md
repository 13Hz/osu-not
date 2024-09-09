# Osu!Not
Телеграм-бот для получения уведомлений о сыгранных ранее игр по выбранным пользователям

### Окружение
* Laravel 11.12.0
* PHP 8.2.21
* MariaDB 10.8

### Установка
1. Склонировать репозиторий
```
git clone https://github.com/13Hz/osu-not.git
```
2. Выполнить установку библиотек и зависимостей
```
composer install
```
3. ~~Выполнить команду установки~~
```
php artisan install
```
Следовать шагам установки: указать идентификатор телеграм бота (без знака @), токен бота, идентификатор приложения авторизации osu oauth, секретный токен.

В процессе установки скрипт автоматически попытается установить webhook для телеграм бота и создать ссылку для создания первого токена авторизации в osu!, но для этого важно указать действующую ссылку на это приложение (APP_URL в env файле (будет предложено указать в процессе установки)). Если по каким-то причинам эти моменты будут отработаны некорректно их всегда можно вызвать вручную командами:

Установка вебхука
```
php artisan webhook:set
```

Генерация ссылки авторизации
```
php artisan api:oauth
```

В конце установки (или после вызова соответствующей команды) будет сгенерирована ссылка на авторизацию через OAuth сервисы osu!. Перед переходом по этой ссылке необходимо установить callback_url в настройках приложения на сайте, что бы произошел автоматический редирект на приложение и токен добавился в систему

4. Запустить обработчик планировщика заданий
```
php artisan schedule:work
```
5. После установки можно перейти в чат с ботом или добавить его в любой чат/канал и работать с ним, доступные команды и их описание будет ниже

### Команды
```
/start - Инициализация и запуск бота
/add {username} - Добавить пользователя для отслеживания в текущем чате
```

### Описание работы
После добавления пользователя в чат он будет периодически проходиться скриптом для проверки новых результатов. Результатом считается последняя сыгранная карта (а если точнее - хэш из полей: дата игры, макс. комбо и аккуратность), если в текущей итерации проверки результат отличается от того, что был итерацией ранее - считаем этот результат новым, обновляем эту информацию и отправляем сообщения во все чаты, где был добавлен этот игрок

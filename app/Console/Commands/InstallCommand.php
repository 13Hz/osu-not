<?php

namespace App\Console\Commands;

use App\Http\Services\OsuTokenService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;
use Jackiedo\DotenvEditor\DotenvEditor;
use Psr\Log\LoggerInterface;

class InstallCommand extends Command
{
    protected $signature = 'install';

    protected $description = 'Команда первоначальной установки';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly DotenvEditor    $dotenvEditor,
        private readonly Artisan         $artisan
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->alert('Средство установки');
        try {
            $this->initializeEnv();
            $this->setupDatabase();
            $this->migrateDatabase();
            $this->configureApp();
            $this->getOauthTokenLink();
        } catch (\Exception $ex) {
            $this->logger->error($ex);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function initializeEnv(): void
    {
        if (!file_exists(base_path('.env'))) {
            $this->components->task('Копирую .env файл', fn() => copy(base_path('.env.example'), base_path('.env')));
        } else {
            $this->components->info('.env файл уже существует - пропуск');
        }

        if (!empty(env('APP_KEY'))) {
            $this->components->info('Ключ уже сгенерирован - пропуск');
        } else {
            $this->components->task('Генерирую ключ', fn() => $this->artisan->call('key:generate'));
        }

        $this->dotenvEditor->load(base_path('.env'));
    }

    private function setupDatabase(): void
    {
        $config = [
            'DB_HOST' => '',
            'DB_PORT' => '',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
        ];

        $config['DB_CONNECTION'] = $this->choice(
            'Какой драйвер БД использовать?',
            [
                'mysql' => 'MySQL/MariaDB',
                'pgsql' => 'PostgreSQL',
                'sqlsrv' => 'SQL Server',
                'sqlite-e2e' => 'SQLite',
            ],
            'mysql'
        );

        if ($config['DB_CONNECTION'] === 'sqlite-e2e') {
            $config['DB_DATABASE'] = $this->ask('Укажите абсолютный путь до файла базы данных');
        } else {
            $config['DB_HOST'] = (string)$this->ask('Хост БД', '127.0.0.1');
            $config['DB_PORT'] = (string)$this->ask('Порт БД', '3306');
            $config['DB_DATABASE'] = (string)$this->ask('Имя БД', 'osu-not');
            $config['DB_USERNAME'] = (string)$this->ask('Пользователь БД', 'root');
            $config['DB_PASSWORD'] = (string)$this->ask('Пароль БД');
        }

        foreach ($config as $key => $value) {
            $this->dotenvEditor->setKey($key, $value);
        }

        $this->dotenvEditor->save();

        config([
            'database.default' => $config['DB_CONNECTION'],
            "database.connections.{$config['DB_CONNECTION']}.host" => $config['DB_HOST'],
            "database.connections.{$config['DB_CONNECTION']}.port" => $config['DB_PORT'],
            "database.connections.{$config['DB_CONNECTION']}.database" => $config['DB_DATABASE'],
            "database.connections.{$config['DB_CONNECTION']}.username" => $config['DB_USERNAME'],
            "database.connections.{$config['DB_CONNECTION']}.password" => $config['DB_PASSWORD'],
        ]);
    }

    private function migrateDatabase(): void
    {
        $this->components->task('Устанавливаю базу данных', fn() => $this->artisan->call('migrate:fresh', ['--force' => true, '--seed' => true]));
    }

    private function configureApp(): void
    {
        $appUrl = (string)$this->ask('Введите URL приложения', 'http://example.com');
        $telegramBotToken = (string)$this->ask('Введите токен телеграм бота', '');
        $telegramBotName = (string)$this->ask('Введите идентификатор бота без @', '');
        $osuApiClientId = (string)$this->ask('Введите идентификатор приложения osu', '');
        $osuApiClientSecret = (string)$this->ask('Введите секретный ключ приложения osu', '');
        $this->dotenvEditor->setKeys([
            'APP_URL' => $appUrl,
            'TELEGRAM_BOT_TOKEN' => $telegramBotToken,
            'TELEGRAM_BOT_NAME' => $telegramBotName,
            'OSU_API_CLIENT_ID' => $osuApiClientId,
            'OSU_API_CLIENT_SECRET' => $osuApiClientSecret,
        ]);
        $this->dotenvEditor->save();
    }

    private function getOauthTokenLink(): void
    {
        $this->components->task('Обновляю конфигурацию', function () {
            $this->artisan->call('config:clear');
            $this->artisan->call('config:cache');
        });
        $this->alert(OsuTokenService::getOauthLink());
    }
}

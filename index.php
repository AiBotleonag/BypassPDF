<?php
require_once __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Load environment variables
$bot_token = getenv('BOT_TOKEN') ?: 'YOUR_BOT_TOKEN_HERE';
$webhook_url = getenv('WEBHOOK_URL') ?: '';

// Initialize logger
$log = new Logger('bot');
$log->pushHandler(new StreamHandler(__DIR__ . '/error.log', Logger::DEBUG));
TelegramLog::initialize($log);

try {
    // Create Telegram API object
    $telegram = new Telegram($bot_token, 'BasicPlusBot');
    
    // Enable MySQL if database URL is provided
    if (getenv('DATABASE_URL')) {
        $telegram->enableMySql([
            'host'     => parse_url(getenv('DATABASE_URL'), PHP_URL_HOST),
            'user'     => parse_url(getenv('DATABASE_URL'), PHP_URL_USER),
            'password' => parse_url(getenv('DATABASE_URL'), PHP_URL_PASS),
            'database' => substr(parse_url(getenv('DATABASE_URL'), PHP_URL_PATH), 1)
        ]);
    } else {
        // Use file-based storage (user.json)
        $telegram->enableAdmin(0); // Your user ID
    }

    // Handle webhook or long polling
    if (php_sapi_name() === 'cli') {
        // CLI mode - long polling
        $telegram->useGetUpdatesWithoutDatabase();
        while (true) {
            $response = $telegram->handleGetUpdates();
            if (!$response->isOk()) {
                $log->error('Long polling error: ' . $response->getDescription());
            }
            sleep(1);
        }
    } else {
        // Web mode - webhook
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telegram->handle();
        } else {
            // Set or delete webhook
            if (isset($_GET['setwebhook'])) {
                $result = $telegram->setWebhook($webhook_url);
                echo $result->getDescription();
            } elseif (isset($_GET['deletewebhook'])) {
                $result = $telegram->deleteWebhook();
                echo $result->getDescription();
            } else {
                echo "Bot is running! Use ?setwebhook to activate.";
            }
        }
    }

} catch (Exception $e) {
    $log->error('Bot error: ' . $e->getMessage());
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo "Error occurred - check logs";
    }
}
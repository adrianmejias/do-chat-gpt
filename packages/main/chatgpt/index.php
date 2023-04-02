<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\ChatGpt;
use Orhanerday\OpenAi\OpenAi;

/**
 * Main function.
 *
 * @param array $args
 * @return array
 */
function main(array $args): array
{
    $dotenv = Dotenv::createMutable(__DIR__);
    $dotenv->safeLoad();

    $openAiKey = $_ENV['OPENAI_API_KEY'] ?? env('OPENAI_API_KEY', '');

    if (empty($openAiKey)) {
        return [
            'body' => [
                'error' => 'No OpenAI API key provided.',
            ],
        ];
    }

    $systemMessage = $_ENV['SYSTEM_MESSAGE'] ?? env('SYSTEM_MESSAGE', '');

    if (empty($systemMessage)) {
        return [
            'body' => [
                'error' => 'No system message provided.',
            ],
        ];
    }

    $openAi = new OpenAi($openAiKey);
    $chatGpt = new ChatGpt($openAi, $systemMessage);

    if (isset($args['clear'])) {
        $chatGpt->clearDocuments();

        return [
            'body' => [
                'message' => 'Documents cleared.',
            ],
        ];
    }

    if (isset($_FILES['document'])) {
        $legalDocuments = $_FILES['document'];

        return [
            'body' => $chatGpt->addDocument($legalDocuments),
        ];
    }

    if (isset($args['content'])) {
        return [
            'body' => $chatGpt->communicate($args['content']),
        ];
    }

    return [
        'body' => [
            'error' => 'No content provided.',
        ],
    ];
}

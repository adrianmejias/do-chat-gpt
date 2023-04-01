<?php

use Dotenv\Dotenv;
use App\ChatGpt;
use Orhanerday\OpenAi\OpenAi;

$dotenv = Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();

beforeEach(function () {
    $this->openAiKey = env('OPENAI_API_KEY', '');
    $this->systemMessage = env('SYSTEM_MESSAGE', '');

    $this->openAi = new OpenAi($this->openAiKey);
    $this->chatgpt = new ChatGpt($this->openAi, $this->systemMessage);

    $_SESSION['documents'] = [];
});

test('if open ai key is not empty', function () {
    expect($this->openAiKey)->not()->toBeEmpty();
});

test('if system message is not empty', function () {
    expect($this->systemMessage)->not()->toBeEmpty();
});

test('if chatgpt can be created', function () {
    expect($this->chatgpt)->toBeInstanceOf(ChatGpt::class);
});

test('if chatgpt can add document', function () {
    $this->chatgpt->addDocument([
        'name' => 'document.txt',
        'tmp_name' => __DIR__ . '/document.txt',
        'size' => filesize(__DIR__ . '/document.txt'),
    ]);
    expect($_SESSION['documents'])->toBeArray()->not()->toBeEmpty();
});

test('if chatgpt can get documents', function () {
    $this->chatgpt->addDocument([
        'name' => 'document.txt',
        'tmp_name' => __DIR__ . '/document.txt',
        'size' => filesize(__DIR__ . '/document.txt'),
    ]);
    $documents = $this->chatgpt->getDocuments();
    expect($documents)->toBeArray()->not()->toBeEmpty();
});

test('if chatgpt can clear documents', function () {
    $this->chatgpt->addDocument([
        'name' => 'document.txt',
        'tmp_name' => __DIR__ . '/document.txt',
        'size' => filesize(__DIR__ . '/document.txt'),
    ]);
    $this->chatgpt->clearDocuments();
    expect($_SESSION['documents'])->toBeArray()->toBeEmpty();
});

test('if chatgpt can communicate', function () {
    $response = $this->chatgpt->communicate('What programming languages should I learn if I want to program AI?');
    expect($response)->toBeString()->not()->toBeEmpty();
});

test('if chatgpt can communicate with documents', function () {
    $this->chatgpt->addDocument([
        'name' => 'document.txt',
        'tmp_name' => __DIR__ . '/document.txt',
        'size' => filesize(__DIR__ . '/document.txt'),
    ]);
    $response = $this->chatgpt->communicate('Should I get certified?');
    expect($response)->toBeString()->not()->toBeEmpty();
});

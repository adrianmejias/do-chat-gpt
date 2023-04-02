<?php

namespace Tests\Feature;

use App\ChatGpt;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Support\Collection;

beforeEach(function () {
    $openAiKey = env('OPENAI_API_KEY', '');
    $systemMessage = env('SYSTEM_MESSAGE', '');

    $openAi = new OpenAi($openAiKey);
    $this->chatgpt = new ChatGpt($openAi, $systemMessage);

    $document = __DIR__ . '/document.txt';
    $this->document = [
        'name' => basename($document),
        'tmp_name' => $document,
        'size' => filesize($document),
    ];

    $_SESSION['documents'] = [];
});

test('if chatgpt is loaded', function () {
    expect($this->chatgpt)->toBeInstanceOf(ChatGpt::class);
});

test('if chatgpt can add document', function () {
    $this->chatgpt->addDocument($this->document);
    expect($_SESSION['documents'])->toBeArray()->not()->toBeEmpty();
});

test('if chatgpt can add document with same name', function () {
    $this->chatgpt->addDocument($this->document);
    $response = $this->chatgpt->addDocument($this->document);
    expect($response)->toBeArray()->toHaveKey('error');
});

test('if chatgpt can get documents', function () {
    $this->chatgpt->addDocument($this->document);
    $documents = $this->chatgpt->getDocuments();
    expect($documents)->toBeArray()->not()->toBeEmpty();
});

test('if chatgpt can clear documents', function () {
    $this->chatgpt->addDocument($this->document);
    $this->chatgpt->clearDocuments();
    expect($_SESSION['documents'])->toBeArray()->toBeEmpty();
});

test('if chatgpt can get document parts', function () {
    $this->chatgpt->addDocument($this->document);
    $documentParts = $this->chatgpt->getDocumentParts();
    expect($documentParts)->toBeInstanceOf(Collection::class)->not()->toBeEmpty();
});

test('if chatgpt can communicate', function () {
    $response = $this->chatgpt->communicate('What programming languages should I learn if I want to program AI?');
    expect($response)->toBeString()->not()->toBeEmpty();
});

test('if chatgpt can communicate with documents', function () {
    $this->chatgpt->addDocument($this->document);
    $response = $this->chatgpt->communicate('Should I get certified?');
    expect($response)->toBeString()->not()->toBeEmpty();
});

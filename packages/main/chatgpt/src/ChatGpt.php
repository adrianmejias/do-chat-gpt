<?php

namespace App;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Orhanerday\OpenAi\OpenAi;
use App\Contracts\ChatGptContract;

final class ChatGpt implements ChatGptContract
{
    public function __construct(
        public OpenAi $openAi,
        public string $systemMessage
    ) {
        if (empty($this->systemMessage)) {
            throw new Exception('System message is empty.');
        }

        if (session_status() !== PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['documents'])) {
            $_SESSION['documents'] = [];
        }
    }

    public function addDocument(array $document): string|array
    {
        $documents = Collection::make($this->getDocuments());
        $existingDocument = $documents->filter(fn ($doc) => $doc['name'] === $document['name']);

        if ($existingDocument->count() > 0) {
            return [
                'error' => 'Document already exists: ' . $document['name'],
            ];
        }

        $_SESSION['documents'][] = [
            'name' => $document['name'],
            'content' => file_get_contents($document['tmp_name']),
            'size' => $document['size'],
        ];

        return 'Document added: ' . $document['name'];
    }

    public function getDocuments(): array
    {
        return $_SESSION['documents'] ?? [];
    }

    public function clearDocuments(): void
    {
        $_SESSION['documents'] = [];
    }

    public function getDocumentParts(): Collection
    {
        $documents = Collection::make($this->getDocuments());

        if ($documents->count() > 0) {
            return $documents->map(function ($document) {
                return str_split($document['content'], 2048);
            });
        }

        return Collection::make([]);
    }

    public function communicate(string $content): string|array
    {
        if (empty($content)) {
            return [
                'error' => 'Content is empty.',
            ];
        }

        $chatMessages = [];

        $chatMessages[] = [
            'role' => 'system',
            'content' => $this->systemMessage,
        ];

        $this->getDocumentParts()->each(function ($part) use (&$chatMessages) {
            Collection::make($part)->each(function ($part) use (&$chatMessages) {
                $chatMessages[] = [
                    'role' => 'user',
                    'content' => $part,
                ];
            });
        });

        $chatMessages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        try {
            $chat = $this->openAi->chat([
                'model' => 'gpt-3.5-turbo',
                'messages' => $chatMessages,
                'temperature' => 0.5,
                'max_tokens' => 1024,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }

        if ($response = json_decode($chat, true)) {
            return Arr::get($response, 'choices.0.message.content', '');
        }

        return '';
    }
}

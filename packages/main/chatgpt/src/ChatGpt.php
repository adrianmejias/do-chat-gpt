<?php

namespace App;

use Exception;

final class ChatGpt implements ChatGptContract
{
    public function __construct(
        public \Orhanerday\OpenAi\OpenAi $openAi,
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
        $exists = array_filter($this->getDocuments(), fn ($doc) => $doc['name'] === $document['name']);

        if (count($exists) > 0) {
            return [
                'error' => 'Document already exists: ' . $document['name'],
            ];
        }

        $_SESSION['documents'][] = [
            'name' => $document['name'],
            'content' => file_get_contents($document['tmp_name']),
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

    public function communicate(string $content): string|array
    {
        $chatMessages = [];

        $chatMessages[] = [
            'role' => 'system',
            'content' => $this->systemMessage,
        ];

        $documents = $this->getDocuments();

        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $parts = mb_str_split($document['content'], 2048);

                if (count($parts) > 1) {
                    foreach ($parts as $part) {
                        $chatMessages[] = [
                            'role' => 'user',
                            'content' => $part,
                        ];
                    }
                }
            }
        }

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
                'user' => getClientIp(),
            ]);
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        $response = json_decode($chat);
        $assistantMessage =  $response->choices[0]->message->content ?? '';

        return $assistantMessage;
    }
}

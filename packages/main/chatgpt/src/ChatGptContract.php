<?php

namespace App;

interface ChatGptContract
{
    /**
     * Constructor
     *
     * @param \Orhanerday\OpenAi\OpenAi $openAi
     * @param string $systemMessage
     * @return void
     */
    public function __construct(\Orhanerday\OpenAi\OpenAi $openAi, string $systemMessage);

    /**
     * Add document to session
     *
     * @param array $document
     * @return string|array<string, string>
     */
    public function addDocument(array $document): string|array;

    /**
     * Get documents from session
     *
     * @return array<string>
     */
    public function getDocuments(): array;

    /**
     * Clear documents from session
     *
     * @return void
     */
    public function clearDocuments(): void;

    /**
     * Communicate with OpenAI
     *
     * @param string $content
     * @return string|array<string, string>
     */
    public function communicate(string $content): string|array;
}

<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ChatGptContract
{
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
     * Get document parts
     *
     * @return \Illuminate\Support\Collection<string>
     */
    public function getDocumentParts(): Collection;

    /**
     * Communicate with OpenAI
     *
     * @param string $content
     * @return string|array<string, string>
     */
    public function communicate(string $content): string|array;
}

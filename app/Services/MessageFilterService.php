<?php
namespace App\Services;

class MessageFilterService
{
    private array $patterns = [
        '/[a-zA-Z0-9._%+\-]+\s*[@＠]\s*[a-zA-Z0-9.\-]+\s*\.[a-zA-Z]{2,}/u',
        '/\(at\)|\[at\]|\{at\}|\s+at\s+/i',
        '/(\+?\d[\s\-.]?){7,15}/',
        '/\b\d{4,}\b/',
        '/[a-zA-Z0-9\-]+\.(com|de|eu|net|org|io|app|sex|xxx|chat|live|me|to|co|uk|fr|it|es|ru|nl|be|at|ch)\b/i',
        '/[a-zA-Z]{2,}\.[a-zA-Z]{2,}/',
        '/[a-zA-Z0-9._%+\-]+\s*\(dot\)\s*[a-zA-Z]{2,}/i',
        '/wa(tz?|pp|hats)\s*(app)?\s*:?\s*\d/i',
        '/telegram|signal|wickr|snapchat|kik\s*:?\s*\w/i',
    ];

    public function containsContactInfo(string $text): bool
    {
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        return false;
    }

    public function filterMessage(string $text): array
    {
        $blocked = $this->containsContactInfo($text);
        return ['blocked' => $blocked, 'text' => $text];
    }
}

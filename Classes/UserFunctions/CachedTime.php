<?php
declare(strict_types=1);
namespace Machwert\TccdExamples\UserFunctions;

#Extending Existing Functionality [16] - 3. UserFunctions

final class CachedTime {
    /**
     * Output the current time in red letters
     *
     * @param string          Empty string (no content to process)
     * @param array           TypoScript configuration
     * @return        string          HTML output, showing the current server time.
     */
    public function printTime(string $content, array $conf): string
    {
        return '<div class="outer"><p>Last cached: ' . date('Y-m-d H:i:s') . '</p></div>';
    }
}
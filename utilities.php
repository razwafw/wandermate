<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

function parseNewlineList($str): array
{
    return explode("\n", $str);
}

function parseItinerary($str): array
{
    $result = [];

    foreach (parseNewlineList($str) as $line) {
        $parts = explode('|', $line, 2);
        $day = isset($parts[0]) ? trim($parts[0]) : '';
        $desc = isset($parts[1]) ? trim($parts[1]) : '';
        if ($day !== '' && $desc !== '') {
            $result[$day] = $desc;
        }
    }

    return $result;
}
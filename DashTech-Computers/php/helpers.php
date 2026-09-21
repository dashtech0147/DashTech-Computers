<?php

function clean($value)
{
    return trim((string) $value);
}

function logError($message)
{
    $file = __DIR__ . '/../logs/email-errors.log';

    $time = date('Y-m-d H:i:s');

    file_put_contents(
        $file,
        "[$time] $message" . PHP_EOL,
        FILE_APPEND
    );
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

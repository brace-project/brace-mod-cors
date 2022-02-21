<?php

/**
 * Check if the origin url in $needle matches at least
 * one Origin specified in allowedOrigins
 *
 * @param string $needle
 * @param array|string $allowedOrigins
 * @return bool
 */
function origin_match (string $needle, array|string $allowedOrigins) : bool
{
    if (is_string($allowedOrigins))
        $allowedOrigins = [$allowedOrigins];

    $needle = parse_url($needle);
    if ($needle === false)
        return false;

    foreach ($allowedOrigins as $allowedOrigin) {
        $allowedOrigin = parse_url($allowedOrigin);
        if ($allowedOrigin === false)
            continue;
        if ($needle[PHP_URL_SCHEME] === $allowedOrigin[PHP_URL_SCHEME] && $needle[PHP_URL_HOST] === $allowedOrigin[PHP_URL_HOST])
            return true;
    }
    return false;
}

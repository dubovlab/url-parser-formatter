<?php declare(strict_types=1);
/*
 * This file is part of Url parsing and formatting library.
 *
 * (c) dubovlab <dubovlab@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace url;

/**
 * @param array|string $str_or_list
 * @param string|null $base_uri
 * @return string[]
 */
function parse_and_format_all($str_or_list, ?string $base_uri = null)
{
    if (is_array($str_or_list)) {
        return array_map(function ($str) use ($base_uri) {
            return format(parse($str, $base_uri));
        }, $str_or_list);
    }
    else {
        return format(parse($str_or_list, $base_uri));
    }
}

function parse_query($query)
{
    parse_str($query, $components);
    return is_array($components) ? $components : [];
}

function is_empty_path($path)
{
    return ltrim((string)$path, '/') === '';
}

function normalize_path($path)
{
    die('todo: normalize_path()');
}

function parse_domain($str, $level = null)
{
    $components = parse($str);
    $domain = $components['host'] ?? null;
    if (null !== $domain && null !== $level) {
        $domain = implode('.', array_slice(explode('.', $domain), -1 * $level));
    }
    return $domain;
}

/** @return string|void */
function to_dataurl($bytes)
{
    if ($fi = finfo_open(FILEINFO_MIME)) {
        $mime = finfo_buffer($fi, $bytes, FILEINFO_MIME_TYPE);
        $base64 = base64_encode($bytes);
        return "data:$mime;base64,$base64";
    }
}

/** @return array|void */
function parse_dataurl($dataurl)
{
    if (preg_match('~^data:(\w+)/(\w+);base64,(.*)$~si', $dataurl, $m)) {
        list(, $mime, $type, $base64) = $m;
        $bytes = base64_decode($base64);
        return [$bytes, "$mime/$type"];
    }
}

function to_proxyline($url)
{
    $components = parse_url((string)$url);
    if (!$components || !isset($components['host'])) {
        return null;
    }

    $line = $components['scheme'] . '://' . $components['host'] . ':' . (int)($components['port'] ?? 80) ?: 80;
    if ($user = $components['user'] ?? null and $pass = $components['pass'] ?? null) {
        $line .= ':' . $components['user'] . ':' . $components['pass'];
    }
    if ($fragment = $components['fragment'] ?? null) {
        $line .= ':' . $fragment;
    }
    return $line;
}

function parse_proxyline($line, $protocol = null)
{
    if (null === $protocol) {
        list($protocol, $line) = explode('://', $line, 2);
    }
    $components = array_map('trim', explode(':', $line, 6));
    switch (count($components)) {
        default:
            return null;

        case 1:
            $host = $components[0];
            return "$protocol://$host";

        case 2:
            list($host, $port) = $components;
            return "$protocol://$host:$port";

        case 3:
            list($host, $port, $fragment) = $components;
            return "$protocol://$host:$port#$fragment";

        case 4:
            list($host, $port, $user, $pass) = $components;
            return "$protocol://$user:$pass@$host:$port";

        case 5:
            list($host, $port, $user, $pass, $fragment) = $components;
            return "$protocol://$user:$pass@$host:$port#$fragment";
    }
}
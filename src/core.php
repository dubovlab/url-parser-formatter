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

function parse($str, $base_url = null)
{
    $str = str_replace(array(' ', "\n", "\r"), array('%20', '', ''), $str . '');
    $components = parse_url($str);
    if (!$components)
        return null;

    if ('' === (string)$base_url)
        return $components;

    $host = $components['host'] ?? '';
    if ('' !== $host) {
        if (!isset($components['scheme'])) {
            $base_components = parse_url($base_url);
            $components['scheme'] = $base_components['scheme'] ?? 'http';
        }
        return $components;
    }

    $base_components = parse_url($base_url);
    if (!$base_components)
        return null;

    $path = $components['path'] ?? null;
    $base_components['query'] = $components['query'] ?? null;
    $base_components['fragment'] = $components['fragment'] ?? null;

    if (substr($path, 0, 1) === '/') {
        $base_components['path'] = $path;
        return $base_components;
    }

    $base_path = $base_components['path'] ?? null;
    if (substr($base_path, strlen($base_path) - 1, 1) !== '/') {
        $index = strrpos($base_path, '/');
        $base_path = false === $index ? '/' : substr($base_path, 0, $index + 1);
    }
    $base_components['path'] = $base_path . $path;
    return $base_components;
}

function format(array $components, ?array $remove_components = null, ?array $retain_components = null)
{
    if ($remove_components) {
        $components = array_diff_key($components, array_fill_keys($remove_components, null));
    }
    if (is_array($retain_components)) {
        $components = array_intersect_key($components, array_fill_keys($retain_components, null));
    }

    $str = '';
    if ($scheme = $components['scheme'] ?? null) $str .= $scheme . '://';
    if ($user = $components['user'] ?? null) $str .= $user;
    if ($pass = $components['pass'] ?? null and $user) $str .= ':' . $pass;
    if ($user or $pass) $str .= '@';
    if ($host = $components['host'] ?? null) $str .= $host;
    if ($port = $components['port'] ?? null) $str .= ':' . $port;
    if ($path = $components['path'] ?? null) $str .= $path;

    if (is_array($query = $components['query_components'] ?? null)) {
        if ($query) {
            $str .= '?' . http_build_query($query);
        }
    }
    else if ('' !== (string)($query = $components['query'] ?? null)) {
        $str .= '?' . $query;
    }

    if (is_array($fragment = $components['fragment_components'] ?? null)) {
        if ($fragment) {
            $str .= '#' . http_build_query($fragment);
        }
    }
    else if ('' !== (string)($fragment = $components['fragment'] ?? null)) {
        $str .= '#' . $fragment;
    }

    return $str;
}

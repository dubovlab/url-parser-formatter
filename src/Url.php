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

class Url
{
    static function parse($str, $base_url = null)
    {
        return new Url($str, parse($str, $base_url));
    }

    var $origin;

    var $scheme;
    var $host;
    var $port;
    var $user;
    var $pass;
    var $path;
    var $query;
    var $query_components;
    var $fragment;
    var $fragment_components;

    private function __construct($origin = null, array $components = null)
    {
        $this->origin = $origin;

        if ($components)
            foreach ($components as $k => $v)
                $this->$k = $v;
    }

    function __toString()
    {
        return format((array)$this);
    }

    function format(?array $remove = null, ?array $retain = null)
    {
        return format((array)$this, $remove, $retain);
    }

    function query($k = null)
    {
        if (null === $this->query_components) {
            $this->query_components = parse_query($this->query);
        }
        return null !== $k ? ($this->query_components[$k] ?? null) : $this->query_components;
    }

    function fragment($k = null)
    {
        if (null === $this->fragment_components) {
            $this->fragment_components = parse_query($this->fragment);
        }
        return null !== $k ? $this->fragment_components[$k] : $this->fragment_components;
    }

    function normalize_query(?array $remove = null, ?array $retain = null)
    {
        if ($remove) {
            $this->query_components = array_diff_key($this->query(), array_fill_keys($remove, null));
        }
        if (is_array($retain)) {
            $this->query_components = array_intersect_key($this->query(), array_fill_keys($remove, null));
        }
        return $this;
    }

    function normalize_fragment(?array $remove = null, ?array $retain = null)
    {
        if ($remove) {
            $this->fragment_components = array_diff_key($this->fragment(), array_fill_keys($remove, null));
        }
        if (is_array($retain)) {
            $this->fragment_components = array_intersect_key($this->fragment(), array_fill_keys($remove, null));
        }
        return $this;
    }

    function normalize(?array $remove = null, ?array $retain = null)
    {
        return $this
            ->normalize_query($remove, $retain)
            ->normalize_fragment($remove, $retain);
    }
}

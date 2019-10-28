<?php declare(strict_types=1);
/*
 * This file is part of Url parsing and formatting library.
 *
 * (c) dubovlab <dubovlab@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UrlTest extends PHPUnit\Framework\TestCase
{
    function testParseAndFormat()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/file.ext',
            'query' => 'query_key=query_value&query_key2=query_value2',
            'query_components' => null,
            'fragment' => 'fragment_key=fragment_value&fragment_key2=fragment_value2',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame($src_url, (string)$url);
    }

    function testBaseUrl()
    {
        $src_url = 'scheme2://user2:pass2@host2:44444/root2/dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';
        $base_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $result_url = 'scheme2://user2:pass2@host2:44444/root2/dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme2',
            'host' => 'host2',
            'port' => 44444,
            'user' => 'user2',
            'pass' => 'pass2',
            'path' => '/root2/dir2/file2.ext',
            'query' => 'query_key3=query_value3&query_key4=query_value4',
            'query_components' => null,
            'fragment' => 'fragment_key3=fragment_value3&fragment_key4=fragment_value4',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url, $base_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame($src_url, (string)$url);
    }

    function testAbsolutePathWithBaseUrl()
    {
        $src_url = '/root2/dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';
        $base_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $result_url = 'scheme://user:pass@host:55555/root2/dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';;
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root2/dir2/file2.ext',
            'query' => 'query_key3=query_value3&query_key4=query_value4',
            'query_components' => null,
            'fragment' => 'fragment_key3=fragment_value3&fragment_key4=fragment_value4',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url, $base_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame($result_url, (string)$url);
    }

    function testRelativePathWithBaseUrl()
    {
        $src_url = 'dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';
        $base_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $result_url = 'scheme://user:pass@host:55555/root/dir/dir2/file2.ext?query_key3=query_value3&query_key4=query_value4#fragment_key3=fragment_value3&fragment_key4=fragment_value4';;
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/dir2/file2.ext',
            'query' => 'query_key3=query_value3&query_key4=query_value4',
            'query_components' => null,
            'fragment' => 'fragment_key3=fragment_value3&fragment_key4=fragment_value4',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url, $base_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame($result_url, (string)$url);
    }

    function testRetainFormat()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/file.ext',
            'query' => 'query_key=query_value&query_key2=query_value2',
            'query_components' => null,
            'fragment' => 'fragment_key=fragment_value&fragment_key2=fragment_value2',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame('scheme://', $url->format(null, ['scheme']));
        $this->assertSame('user:pass@', $url->format(null, ['user', 'pass']));
        $this->assertSame('host:55555', $url->format(null, ['host', 'port']));
        $this->assertSame('/root/dir/file.ext', $url->format(null, ['path']));
        $this->assertSame('?query_key=query_value&query_key2=query_value2', $url->format(null, ['query']));
        $this->assertSame('#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(null, ['fragment']));
    }

    function testRemoveFormat()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/file.ext',
            'query' => 'query_key=query_value&query_key2=query_value2',
            'query_components' => null,
            'fragment' => 'fragment_key=fragment_value&fragment_key2=fragment_value2',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url);
        $this->assertSame($components, (array)$url);
        $this->assertSame('user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(['scheme']));
        $this->assertSame('scheme://host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(['user', 'pass']));
        $this->assertSame('scheme://user:pass@/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(['host', 'port']));
        $this->assertSame('scheme://user:pass@host:55555?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(['path']));
        $this->assertSame('scheme://user:pass@host:55555/root/dir/file.ext#fragment_key=fragment_value&fragment_key2=fragment_value2', $url->format(['query']));
        $this->assertSame('scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2', $url->format(['fragment']));
    }

    function testQuery()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/file.ext',
            'query' => 'query_key=query_value&query_key2=query_value2',
            'query_components' => null,
            'fragment' => 'fragment_key=fragment_value&fragment_key2=fragment_value2',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url);
        $this->assertSame('query_value', $url->query('query_key'));
        $this->assertSame('query_value2', $url->query('query_key2'));

        $url->query_components['query_key'] = 'query_value3';
        $this->assertSame('scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value3&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2', (string)$url);
    }

    function testFragment()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $components = [
            'origin' => $src_url,
            'scheme' => 'scheme',
            'host' => 'host',
            'port' => 55555,
            'user' => 'user',
            'pass' => 'pass',
            'path' => '/root/dir/file.ext',
            'query' => 'query_key=query_value&query_key2=query_value2',
            'query_components' => null,
            'fragment' => 'fragment_key=fragment_value&fragment_key2=fragment_value2',
            'fragment_components' => null,
        ];

        $url = \url\Url::parse($src_url);
        $this->assertSame('fragment_value', $url->fragment('fragment_key'));
        $this->assertSame('fragment_value2', $url->fragment('fragment_key2'));

        $url->fragment_components['fragment_key'] = 'fragment_value3';
        $this->assertSame('scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value3&fragment_key2=fragment_value2', (string)$url);
    }
}

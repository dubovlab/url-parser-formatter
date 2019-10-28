<?php declare(strict_types=1);
/*
 * This file is part of Url parsing and formatting library.
 *
 * (c) dubovlab <dubovlab@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FunctionsTest extends PHPUnit\Framework\TestCase
{
    function testParseAndFormatAll()
    {
        $src_url = 'scheme://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $this->assertSame($src_url, \url\parse_and_format_all($src_url));
        $this->assertSame([$src_url], \url\parse_and_format_all([$src_url]));
    }

    function testParseDomain()
    {
        $src_url = 'scheme://user:pass@www.host.com:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $this->assertSame('www.host.com', \url\parse_domain($src_url));
        $this->assertSame('www.host.com', \url\parse_domain($src_url, 4));
        $this->assertSame('www.host.com', \url\parse_domain($src_url, 3));
        $this->assertSame('host.com', \url\parse_domain($src_url, 2));
        $this->assertSame('com', \url\parse_domain($src_url, 1));
        $this->assertSame('www.host.com', \url\parse_domain($src_url, 0));
    }

    function testDataUrl()
    {
        $data = 'abracadabra';
        $dataurl = \url\to_dataurl($data);
        $this->assertSame("data:text/plain;base64," . base64_encode($data), $dataurl);
        $this->assertSame([$data, 'text/plain'], \url\parse_dataurl($dataurl));
    }

    function testProxyLine()
    {
        $src_url = 'scheme://user:pass@www.host.com:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $proxy_line = \url\to_proxyline($src_url);
        $this->assertSame('scheme://www.host.com:55555:user:pass:fragment_key=fragment_value&fragment_key2=fragment_value2', $proxy_line);
        $this->assertSame('scheme://user:pass@www.host.com:55555#fragment_key=fragment_value&fragment_key2=fragment_value2', \url\parse_proxyline($proxy_line));

        // no fragment
        $src_url = 'scheme://user:pass@www.host.com:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2';
        $proxy_line = \url\to_proxyline($src_url);
        $this->assertSame('scheme://www.host.com:55555:user:pass', $proxy_line);
        $this->assertSame('scheme://user:pass@www.host.com:55555', \url\parse_proxyline($proxy_line));

        // no authority
        $src_url = 'scheme://www.host.com:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2';
        $proxy_line = \url\to_proxyline($src_url);
        $this->assertSame('scheme://www.host.com:55555', $proxy_line);
        $this->assertSame('scheme://www.host.com:55555', \url\parse_proxyline($proxy_line));

        // no authority with fragment
        $src_url = 'scheme://www.host.com:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
        $proxy_line = \url\to_proxyline($src_url);
        $this->assertSame('scheme://www.host.com:55555:fragment_key=fragment_value&fragment_key2=fragment_value2', $proxy_line);
        $this->assertSame('scheme://www.host.com:55555#fragment_key=fragment_value&fragment_key2=fragment_value2', \url\parse_proxyline($proxy_line));

        // no port
        $src_url = 'scheme://www.host.com/root/dir/file.ext?query_key=query_value&query_key2=query_value2';
        $proxy_line = \url\to_proxyline($src_url);
        $this->assertSame('scheme://www.host.com:80', $proxy_line);
        $this->assertSame('scheme://www.host.com:80', \url\parse_proxyline($proxy_line));
    }
}

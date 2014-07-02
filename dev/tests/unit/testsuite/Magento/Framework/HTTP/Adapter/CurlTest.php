<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\HTTP\Adapter;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    /** @var Curl */
    protected $model;

    /** @var \Closure */
    public static $curlExectClosure;

    protected function setUp()
    {
        $this->model = new \Magento\Framework\HTTP\Adapter\Curl();
    }

    /**
     * @param string $response
     * @dataProvider readDataProvider
     */
    public function testRead($response)
    {
        self::$curlExectClosure = function ($resource) use ($response) {
            return $response;
        };
        $this->assertEquals(file_get_contents(__DIR__ . '/_files/curl_response_expected.txt'), $this->model->read());
    }

    public function readDataProvider()
    {
        return [
            [file_get_contents(__DIR__ . '/_files/curl_response1.txt')],
            [file_get_contents(__DIR__ . '/_files/curl_response2.txt')],
        ];
    }
}

/**
 * Override global PHP function
 *
 * @param mixed $resource
 * @return string
 */
function curl_exec($resource)
{
    return call_user_func(CurlTest::$curlExectClosure, $resource);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler;

use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Pagination
 * @package Magento\Backend\Test\Handler
 */
class Pagination
{
    /**
     * Pattern for searching grid table in cURL response
     *
     * @var string
     */
    protected $regExpPattern;

    /**
     * Url of cURL request
     *
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $pattern
     */
    public function setRegExpPattern($pattern)
    {
        $this->regExpPattern = $pattern;
    }

    /**
     * Setting all Pagination params
     *
     * @param array $params
     */
    public function prepare($params)
    {
        extract($params);
        $this->setUrl($url);
        $this->setRegExpPattern($pattern);
    }

    /**
     * Sort data in grid to define id if more than 20 items in grid
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function getId()
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();
        preg_match($this->regExpPattern, $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find id');
        }
        return $matches[1];
    }
}

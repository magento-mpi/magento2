<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler;

use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Pagination
 * Used to omit possible issue, when searched Id is not on the same page in cURL response
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
     * Setting all Pagination params for Pagination object.
     * Required url for cURL request and regexp pattern for searching in cURL response.
     *
     * @param $url
     * @param $regExpPattern
     */
    public function __construct($url, $regExpPattern)
    {
        $this->url = $url;
        $this->regExpPattern = $regExpPattern;
    }

    /**
     * Retrieves id from cURL response
     *
     * @throws \Exception
     * @return mixed
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

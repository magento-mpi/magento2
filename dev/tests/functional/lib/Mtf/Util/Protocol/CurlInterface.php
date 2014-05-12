<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Protocol;

/**
 * Class CurlInterface
 */
interface CurlInterface
{
    /**
     * HTTP request methods
     */
    const GET   = 'GET';

    const POST  = 'POST';

    /**
     * Add additional option to cURL
     *
     * @param  int $option      the CURLOPT_* constants
     * @param  mixed $value
     */
    public function addOption($option, $value);

    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param string $url
     * @param string $http_ver
     * @param array  $headers
     * @param array  $params
     */
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $params = array());

    /**
     * Read response from server
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server
     */
    public function close();
}

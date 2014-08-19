<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Protocol\CurlTransport;

use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;

/**
 * Abstract Class AbstractDecorator
 * Abstract Class AbstractDecorator for curl transport
 */
abstract class AbstractDecorator implements CurlInterface
{
    /**
     * Curl transport protocol
     *
     * @var CurlTransport
     */
    protected $transport;

    /**
     * Authorize customer on backend
     *
     * @return void
     */
    abstract protected function authorize();

    /**
     * Add additional option to cURL
     *
     * @param  int $option the CURLOPT_* constants
     * @param  mixed $value
     * @return void
     */
    public function addOption($option, $value)
    {
        $this->transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close()
    {
        $this->transport->close();
    }
}

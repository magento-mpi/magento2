<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Util\Protocol\CurlTransport;

/**
 * Curl handler for updating a banner
 *
 * @package Magento\Banner\Test\Handler\Curl
 */
class UpdateBanner extends CreateBanner
{
    /**
     * Post request for updating banner
     *
     * @param Fixture $fixture [optional]
     * @throws \Exception
     * @return null|string banner_id
     */
    public function execute(Fixture $fixture = null)
    {
        $response = $this->postRequest($fixture);
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception('Banner update by curl handler was not successful! Response: ' . $response);
        }
    }
}
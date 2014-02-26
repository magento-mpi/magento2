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

namespace Magento\Customer\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;

/**
 * Class CreateCustomer.
 * Curl handler for creating customer through registration page.
 *
 * @package Magento\Customer\Test\Handler\Curl
 */
class CreateCustomer extends Curl
{
    /**
     * Post request for creating customer
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        $url = $_ENV['app_frontend_url'] . 'customer/account/createpost/?nocookie=true';
        $curl = new CurlTransport();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}

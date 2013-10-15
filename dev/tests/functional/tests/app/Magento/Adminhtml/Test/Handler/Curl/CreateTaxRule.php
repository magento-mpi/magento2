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

namespace Magento\Adminhtml\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;

/**
 * Class CreateCustomer.
 * Curl handler for creating customer through registration page.
 *
 * @package Magento\Adminhtml\Test\Handler\Curl
 */
class CreateTaxRule extends Curl
{
    /**
     * Post request for creating tax rule
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $is_array_params = in_array($key, array('tax_customer_class', 'tax_product_class', 'tax_rate'));
            if ($is_array_params) {
                $fields[$key] = array($field['value']);
            } else {
                $fields[$key] = $field['value'];
            }
        }
        $url = $_ENV['app_backend_url'] . 'admin/tax_rule/save/';
        $curl = new CurlTransport();
        $curl->write(CurlTransport::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}

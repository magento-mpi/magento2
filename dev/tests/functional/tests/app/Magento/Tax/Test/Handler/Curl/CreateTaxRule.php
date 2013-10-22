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

namespace Magento\Tax\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating Tax Rule.
 *
 * @package Magento\Tax\Test\Handler\Curl
 */
class CreateTaxRule extends Curl
{
    protected $_substitution = array(
        'tax_rate'              => 'tax_rate[]',
        'tax_product_class'     => 'tax_product_class[]',
        'tax_customer_class'    => 'tax_customer_class[]'
    );

    /**
     * Returns data for curl POST params
     *
     * @param $fixture
     * @return array
     */
    public function PostParams($fixture)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            if (array_key_exists($key, $this->_substitution)) {
                $fields[$this->_substitution[$key]] = $field['value'];
            } else {
                $fields[$key] = $field['value'];
            }
        }
        return $fields;
    }

    /**
     * Post request for creating tax rate
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/tax_rule/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $this->PostParams($fixture));
        $response = $curl->read();
        $curl->close();
        return $response;
    }
}

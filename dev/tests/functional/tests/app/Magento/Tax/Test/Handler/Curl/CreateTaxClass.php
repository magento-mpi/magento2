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

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating customer and product tax class.
 *
 * @package Magento\Tax\Test\Handler\Curl
 */
class CreateTaxClass extends Curl
{
    /**
     * Post request for creating tax class
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
        $url = $_ENV['app_backend_url'] . 'tax/tax/ajaxSAve/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();
        return $this->_getClassId($response);
    }

    /**
     * Return saved class id if saved
     *
     * @param string $data
     * @return int|null
     */
    protected function _getClassId($data)
    {
        $data = json_decode($data);
        return isset($data->class_id) ? (int)$data->class_id : null;
    }
}

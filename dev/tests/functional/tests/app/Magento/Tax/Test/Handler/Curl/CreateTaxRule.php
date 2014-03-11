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
 * Curl handler for creating Tax Rule.
 *
 * @package Magento\Tax\Test\Handler\Curl
 */
class CreateTaxRule extends Curl
{
    /**
     * Returns data for curl POST params
     *
     * @param $fixture
     * @return array
     */
    public function _getPostParams($fixture)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $value = $this->_getParamValue($field);

            if (null === $value) {
                continue;
            }

            $_key = $this->_getParamKey($field);
            if (null === $_key) {
                $_key = $key;
            }
            $fields[$_key] = $value;
        }
        return $fields;
    }

    /**
     * Return key for request
     *
     * @param array $data
     * @return null|string
     */
    protected function _getParamKey(array $data)
    {
        return isset($data['input_name']) ? $data['input_name'] : null;
    }

    /**
     * Return value for request
     *
     * @param array $data
     * @return null|string
     */
    protected function _getParamValue(array $data)
    {
        if (array_key_exists('input_value', $data)) {
            return $data['input_value'];
        }

        if (array_key_exists('value', $data)) {
            return $data['value'];
        }
        return null;
    }

    /**
     * Post request for creating tax rate
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'tax/rule/save/?back=1';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $this->_getPostParams($fixture));
        $response = $curl->read();
        $curl->close();
        preg_match("~Location: [^\s]*\/rule\/(\d+)~", $response, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}

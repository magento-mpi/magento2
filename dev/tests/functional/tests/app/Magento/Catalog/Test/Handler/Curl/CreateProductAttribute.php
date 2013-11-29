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

namespace Magento\Catalog\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateProductAttribute
 */
class CreateProductAttribute extends Curl
{
    /**
     * Create attribute
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'catalog/product_attribute/save/back/edit/active_tab/main';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $this->_getPostParams($fixture));
        $response = $curl->read();
        $curl->close();

        $id = null;
        if (preg_match('!catalog/product_attribute/save/attribute_id/(\d+)/active_tab/main/!', $response, $matches)) {
            $id = $matches[1];
        }

        $optionIds = array();
        if (preg_match_all(
            '!attributeOption\.add\({"checked":"(.?)*","intype":"radio","id":"(\d+)"!',
            $response,
            $matches
        )) {
            $optionIds = $matches[2];
        }

        return array('attributeId' => $id, 'optionIds' => $optionIds);
    }

    /**
     * Get data for curl POST params
     *
     * @param Fixture $fixture
     * @return array
     */
    public function _getPostParams(Fixture $fixture)
    {
        $data = $this->_prepareParams($fixture->getData('fields'));
        $options = $fixture->getOptions();
        foreach ($options as $option) {
            $data = array_merge($data, $this->_prepareParams($option));
        }
        return $data;
    }

    /**
     * Prepare data for curl POST params
     *
     * @param array $fields
     * @return array
     */
    public function _prepareParams(array $fields)
    {
        $data = array();
        foreach ($fields as $key => $field) {
            $value = $this->_getParamValue($field);

            if (null === $value) {
                continue;
            }

            $_key = $this->_getParamKey($field);
            if (null === $_key) {
                $_key = $key;
            }
            $data[$_key] = $value;
        }
        return $data;
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
}

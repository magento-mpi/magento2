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
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Class Create Configurable Product
 */
class CreateConfigurable extends Curl
{
    /**
     * Prepare data for curl
     *
     * @param ConfigurableProduct $fixture
     * @return array
     */
    protected function _prepareData(ConfigurableProduct $fixture)
    {
        $curlData = array();

        $curlData['product'] = $this->_getProductData($fixture);
        $curlData['product']['configurable_attributes_data'] = $this->_getConfigurableData($fixture);
        $curlData['variations-matrix'] = $this->_getVariationMatrix($fixture);
        $curlData['attributes'] = $fixture->getDataConfig()['attributes']['id'];
        $curlData['affect_configurable_product_attributes'] = 1;
        $curlData['new-variations-attribute-set-id'] = 4;

        $curlEncoded = json_encode($curlData, true);
        $curlEncoded = str_replace('"Yes"', '1', $curlEncoded);
        $curlEncoded = str_replace('"No"', '0', $curlEncoded);

        return json_decode($curlEncoded, true);
    }

    /**
     * Get product data for curl
     *
     * @param ConfigurableProduct $fixture
     * @return array
     */
    protected function _getProductData(ConfigurableProduct $fixture)
    {
        $curlData = array();
        $baseData = $fixture->getData('fields');
        unset($baseData['configurable_attributes_data']);
        unset($baseData['variations-matrix']);
        foreach($baseData as $key => $field) {
            $curlData[$key] = $field['value'];
        }

        $curlData['tax_class_id'] = 2;
        $curlData['quantity_and_stock_status']['is_in_stock'] = 1;
        $curlData['stock_data'] = array(
            'use_config_manage_stock' => 1,
            'is_in_stock' => 1
        );

        return $curlData;
    }

    /**
     * Get configurable product data for curl
     *
     * @param ConfigurableProduct $fixture
     * @return array
     */
    protected function _getConfigurableData(ConfigurableProduct $fixture)
    {
        $configurableAttribute = $fixture->getData('fields/configurable_attributes_data/value');
        $config = $fixture->getDataConfig();
        $curlData = array();

        foreach ($configurableAttribute as $attributeNumber => $attribute) {
            $attributeId = $config['attributes']['id'][$attributeNumber];
            $optionNumber = 0;
            foreach ($attribute as $attributeFieldName => $attributeField) {
                if (isset($attributeField['value'])) {
                    $curlData[$attributeId][$attributeFieldName] = $attributeField['value'];
                } else {
                    $optionsId = $config['options'][$attributeId]['id'][$optionNumber];
                    foreach ($attributeField as $optionName => $optionField) {
                        $curlData[$attributeId]['values'][$optionsId][$optionName] = $optionField['value'];
                    }
                    $curlData[$attributeId]['values'][$optionsId]['value_index'] = $optionsId;
                    ++$optionNumber;
                }
            }
            $curlData[$attributeId]['code'] = $config['attributes'][$attributeId]['code'];
            $curlData[$attributeId]['attribute_id'] = $attributeId;
        }

        return $curlData;
    }

    /**
     * Get variations data for curl
     *
     * @param ConfigurableProduct $fixture
     * @return array
     */
    protected function _getVariationMatrix(ConfigurableProduct $fixture)
    {
        $config = $fixture->getDataConfig();
        $variationData = $fixture->getData('fields/variations-matrix/value');
        $curlData = array();
        $variationNumber = 0;
        foreach ($config['options'] as $attributeId => $options) {
            foreach ($options['id'] as $option) {
                foreach ($variationData[$variationNumber]['value'] as $fieldName => $fieldData) {
                    if ($fieldName == 'qty') {
                        $curlData[$option]['quantity_and_stock_status'][$fieldName] = $fieldData['value'];
                    } else {
                        $curlData[$option][$fieldName] = $fieldData['value'];
                    }
                }
                if (!isset($curlData[$option]['weight']) && $fixture->getData('fields/weight/value')) {
                    $curlData[$option]['weight'] = $fixture->getData('fields/weight/value');
                }
                $curlData[$option]['configurable_attribute'] =
                    '{"' . $config['attributes'][$attributeId]['code'] . '":"' . $option . '"}';
                ++$variationNumber;
            }
        }
        return $curlData;
    }

    /**
     * Create configurable product
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url']
            . 'admin/catalog_product/save/'
            . $fixture->getUrlParams('create_url_params');
        $params = $this->_prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}

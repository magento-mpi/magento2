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

use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

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
        $curlData['variation-matrix'] = $this->_getVariationMatrix($fixture);
        $curlData['attributes'] = $fixture->getDataConfig()['attributes']['id'];

        return $curlData;
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
        unset($baseData['variation-matrix']);
        foreach($baseData as $key => $field) {
            $curlData[$key] = $field['value'];
        }

        /*
        $params['fields']['tax_class_id']['value'] = 2;
        $paramsField = array();
        foreach ($fixture->getData('fields') as $key => $value) {
            if ($key == 'configurable_attributes_data') {
                $paramsField[$key] = $value;
            } else {
                $paramsField[$key] = $value['value'];
            }
        }
        $params = array_replace_recursive($curlData, $params);

        $curlProductData = array(
            'quantity_and_stock_status' => array(
                'is_in_stock' => 1
            ),
            'status' => 1,
            'meta_title' => '',
            'meta_keyword' => '',
            'meta_description' => '',
            'website_ids' => array(
                '0' => 1
            ),
            'msrp_enabled' => 2,
            'msrp_display_actual_price_type' => 4,
            'enable_googlecheckout' => 1,
            'stock_data' => array(
                'use_config_manage_stock' => 1,
                'use_config_enable_qty_increments'=> 1,
                'use_config_qty_increments' => 1,
                'is_in_stock' => 1,
            ),
            'options_container' => 'container2',
            'visibility' => 4,
            'use_config_gift_message_available' => 1,
            'use_config_gift_wrapping_available' => 1,
            'is_returnable' => 2
        );

        $paramsField = array_replace_recursive($curlProductData, $paramsField);
        $params['product'] = array_merge($params['product'], $paramsField);

        return $params;
        */

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
            $optionNumber = 0;
            foreach ($attribute as $attributeFieldName => $attributeField) {
                $attributeId = $config['attributes']['id'][$attributeNumber];
                if (isset($attributeField['value'])) {
                    $optionsId = $config['options'][$attributeId]['id'][$optionNumber];
                    foreach ($attributeField as $optionName => $optionField) {
                        $curlData[$attributeId]['values'][$optionsId][$optionName] = $optionField['value'];
                    }
                    $curlData[$attributeId]['values'][$optionsId]['value_index'] = $optionsId;
                    ++$optionNumber;
                } else {
                    $curlData[$attributeId][$attributeFieldName] = $attributeField['value'];
                }
                $curlData[$attributeId]['attribute_id'] = $attributeId;
            }
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
        $variationData = $fixture->getData('fields/variation-matrix/value');
        $curlData = array();
        $variationNumber = 0;
        foreach ($config['options'] as $attributeId => $options) {
            foreach ($options['id'] as $option) {
                unset($variationData[$variationNumber]['configurable_attribute']);
                foreach ($variationData[$variationNumber] as $fieldName => $fieldData) {
                    if ($fieldName == 'quantity_and_stock_status') {
                        $curlData[$option][$fieldName]['qty'] = $fieldData['qty']['value'];
                    } else {
                        $curlData[$option][$fieldName] = $fieldData['value'];
                    }
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

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
     * Returns transformed data
     *
     * @param ConfigurableProduct $fixture
     * @return mixed
     */
    protected function _prepareData(ConfigurableProduct $fixture)
    {
        $params = $fixture->getData();
        $params['fields']['tax_class_id']['value'] = 2;
        $paramsField = array();
        foreach ($fixture->getData('fields') as $key => $value) {
            if ($key == 'configurable_attributes_data') {
                $paramsField[$key] = $value;
            } else {
                $paramsField[$key] = $value['value'];
            }
        }
        $params['product'] = $paramsField;
        unset($params['fields']);

        $attributeIdPointer = $params['product']['configurable_attributes_data'];
        reset($attributeIdPointer);
        $attributeId = key($attributeIdPointer);
        $attributeOptionIdsPointer = $params['product']['configurable_attributes_data'][$attributeId]['values'];
        reset($attributeOptionIdsPointer);
        $attributeOptionIds = key($attributeOptionIdsPointer);
        $attribute = $fixture->getAttribute('attribute1');
        $attributeCode = $attribute->getAttributeCode();
        $curlData = array(
            'attributes' => array(
                '0' => $attributeId
            ),
            'variations-matrix' => array(
                $attributeOptionIds => array(
                    'configurable_attribute' => '{"' . $attributeCode . '":"' . $attributeOptionIds .  '"}'
                ),
                array(
                    'configurable_attribute' => '{"' . $attributeCode . '":"' . ($attributeOptionIds+1) . '"}'
                )
            )
        );
        $params = array_replace_recursive($curlData, $params);

        $curlProductData = array(
            'quantity_and_stock_status' => array(
                'is_in_stock' => 1
            ),
            'status' => 1,
            'configurable_attributes_data' => array(
                $attributeId => array(
                    'code' => $attributeCode,
                    'attribute_id' => $attributeId,
                    'values' => array(
                        $attributeOptionIds => array(
                            'value_index' => $attributeOptionIds,
                            ),
                        $attributeOptionIds+1 => array(
                            'value_index' => $attributeOptionIds+1
                            )
                        )
                    )
                ),
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

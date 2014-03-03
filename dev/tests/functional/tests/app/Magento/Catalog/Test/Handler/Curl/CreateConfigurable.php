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
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

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
        $curlData['product']['category_ids'] = $fixture->getCategoryIds();

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
        foreach ($baseData as $key => $field) {
            if (isset ($field['input_value'])) {
                $curlData[$key] = $field['input_value'];
            } else if (isset($field['value'])) {
                $curlData[$key] = $field['value'];
            }
        }

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
     * @param Fixture|\Magento\Catalog\Test\Fixture\ConfigurableProduct $fixture [optional]
     * @return mixed|string
     * @throws \Exception
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url']
            . 'catalog/product/save/'
            . $fixture->getUrlParams('create_url_params') . '/popup/1/back/edit';
        $params = $this->_prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}

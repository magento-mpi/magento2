<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for products resource tests
 */
abstract class Api_Catalog_ProductAbstract extends Magento_Test_Webservice
{
    /**
     * Map common fixtures keys to soap wsdl.
     *
     * @var array
     */
    protected $_productAttributesArrayMap = array(
        'tier_price' => array(
            'website_id' => 'website',
            'cust_group' => 'customer_group_id',
            'price_qty'  => 'qty'
        )
    );

    /**
     * Try to create product using API and check received error messages
     *
     * @param array $productData
     * @param array|string $expectedMessages
     */
    protected function _createProductWithErrorMessagesCheck($productData, $expectedMessages)
    {
        try {
            $this->_tryToCreateProductWithApi($productData);
            $this->fail('SoapFault exception was expected to be raised.');
        } catch (SoapFault $e) {
            $this->_checkErrorMessagesInResponse($e, $expectedMessages);
        }
    }

    /**
     * Create product with API
     *
     * @param array $productData
     * @return int
     */
    protected function _createProductWithApi($productData)
    {
        $productId = (int) $this->_tryToCreateProductWithApi($productData);
        $this->assertGreaterThan(0, $productId, 'Response does not contain valid product ID.');
        return $productId;
    }

    /**
     * Try to create product with API request
     *
     * @param array $productData
     * @return int
     */
    protected function _tryToCreateProductWithApi($productData)
    {
        $dataFormattedForRequest = $this->_prepareProductDataForSoap($productData);
        $response = $this->call('product.create', $dataFormattedForRequest);
        return $response;
    }

    /**
     * Map array keys in accordance to soap wsdl.
     *
     * @param array $productData
     * @return array
     */
    protected function _prepareProductDataForSoap($productData)
    {
        $dataFormattedForRequest = array(
            'type' => $productData['type_id'],
            'set'  => $productData['attribute_set_id'],
            'sku'  => $productData['sku'],
            'productData' => array_diff_key($productData, array_flip(array('type_id', 'attribute_set_id', 'sku')))
        );
        foreach ($dataFormattedForRequest['productData'] as $attrCode => &$attrValue) {
            if (in_array($attrCode, array_keys($this->_productAttributesArrayMap)) && is_array($attrValue)) {
                $map = $this->_productAttributesArrayMap[$attrCode];
                foreach ($attrValue as &$arrayItem) {
                    foreach ($map as $arrayKey => $keyMapValue) {
                        if (in_array($arrayKey, $arrayItem)) {
                            $arrayItem[$keyMapValue] = $arrayItem[$arrayKey];
                            unset($arrayItem[$arrayKey]);
                        }
                    }
                }
                unset($arrayItem);
            }
        }

        return $dataFormattedForRequest;
    }

    /**
     * Check if expected messages contained in the SoapFault exception
     *
     * @param SoapFault $e
     * @param array|string $expectedMessages
     */
    protected function _checkErrorMessagesInResponse(SoapFault $e, $expectedMessages)
    {
        $expectedMessages = is_array($expectedMessages) ? $expectedMessages : array($expectedMessages);
        $receivedMessages = explode("\n", $e->getMessage());
        $this->assertMessagesEqual($expectedMessages, $receivedMessages);
    }
}

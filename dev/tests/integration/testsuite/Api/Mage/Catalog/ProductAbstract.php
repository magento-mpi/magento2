<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Abstract class for products resource tests
 */
abstract class Mage_Catalog_ProductAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Default helper for current test suite
     *
     * @var string
     */
    protected $_defaultHelper = 'Helper_Catalog_Product_Simple';

    /** @var array */
    protected $_helpers = array();

    /**
     * Map common fixtures keys to soap wsdl.
     *
     * @var array
     */
    protected $_productAttributesArrayMap = array(
        'tier_price' => array(
            'website_id' => 'website',
            'cust_group' => 'customer_group_id',
            'price_qty' => 'qty'
        )
    );

    /**
     * Get current test suite helper if class name not specified.
     *
     * @param string|null $helperClass
     * @return mixed
     */
    protected function _getHelper($helperClass = null)
    {
        if (is_null($helperClass)) {
            $helperClass = $this->_defaultHelper;
        }

        if (!isset($this->_helpers[$helperClass])) {
            $this->_helpers[$helperClass] = new $helperClass;
        }

        return $this->_helpers[$helperClass];
    }

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
        $productId = (int)$this->_tryToCreateProductWithApi($productData);
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
        $response = Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $dataFormattedForRequest);
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
            'set' => $productData['attribute_set_id'],
            'sku' => $productData['sku'],
            'productData' => array_diff_key(
                $productData,
                array_flip(array('type_id', 'attribute_set_id', 'sku'))
            )
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
        if (isset($dataFormattedForRequest['productData']['tier_price'])) {
            foreach ($dataFormattedForRequest['productData']['tier_price'] as &$tierPriceItem) {
                $tierPriceItem = (object)$tierPriceItem;
            }
        }
        $dataFormattedForRequest['productData'] = (object)$dataFormattedForRequest['productData'];
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

    /**
     * Assert that two products are equal.
     *
     * @param Mage_Catalog_Model_Product $expected
     * @param Mage_Catalog_Model_Product $actual
     */
    public function assertProductEquals(Mage_Catalog_Model_Product $expected, Mage_Catalog_Model_Product $actual)
    {
        foreach ($expected->getData() as $attribute => $value) {
            $this->assertEquals(
                $value,
                $actual->getData($attribute),
                sprintf('Attribute "%s" value does not equal to expected "%s".', $attribute, $value)
            );
        }
    }
}

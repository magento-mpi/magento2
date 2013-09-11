<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/**
 * Abstract class for products resource tests
 */
abstract class Magento_Catalog_Model_Product_Api_TestCaseAbstract extends PHPUnit_Framework_TestCase
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
    protected $_attributesArrayMap = array(
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
        $formattedData = $this->_prepareProductDataForSoap($productData);
        $exception = Magento_TestFramework_Helper_Api::callWithException($this, 'catalogProductCreate', $formattedData);
        $this->_checkErrorMessagesInResponse($exception, $expectedMessages);
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
        $formattedData = $this->_prepareProductDataForSoap($productData);
        $response = Magento_TestFramework_Helper_Api::call($this, 'catalogProductCreate', $formattedData);
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
        $formattedData = array(
            'type' => $productData['type_id'],
            'set' => $productData['attribute_set_id'],
            'sku' => $productData['sku'],
            'productData' => array_diff_key(
                $productData,
                array_flip(array('type_id', 'attribute_set_id', 'sku'))
            )
        );
        foreach ($formattedData['productData'] as $attrCode => &$attrValue) {
            if (in_array($attrCode, array_keys($this->_attributesArrayMap)) && is_array($attrValue)) {
                $map = $this->_attributesArrayMap[$attrCode];
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
        if (isset($formattedData['productData']['tier_price'])) {
            foreach ($formattedData['productData']['tier_price'] as &$tierPriceItem) {
                $tierPriceItem = (object)$tierPriceItem;
            }
        }
        $formattedData['productData'] = (object)$formattedData['productData'];
        return $formattedData;
    }

    /**
     * Check if expected messages contained in the SoapFault exception
     *
     * @param SoapFault $exception
     * @param array|string $expectedMessages
     */
    protected function _checkErrorMessagesInResponse(SoapFault $exception, $expectedMessages)
    {
        $expectedMessages = is_array($expectedMessages) ? $expectedMessages : array($expectedMessages);
        $receivedMessages = explode("\n", $exception->getMessage());
        $this->assertMessagesEqual($expectedMessages, $receivedMessages);
    }

    /**
     * Assert that two products are equal.
     *
     * @param \Magento\Catalog\Model\Product $expected
     * @param \Magento\Catalog\Model\Product $actual
     */
    public function assertProductEquals(\Magento\Catalog\Model\Product $expected, \Magento\Catalog\Model\Product $actual)
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

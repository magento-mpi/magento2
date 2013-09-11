<?php
/**
 * Test configurable product API
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @method Magento_Catalog_Model_Product_Api_Helper_Configurable _getHelper()
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_Model_Product_Api_ConfigurableTest extends Magento_Catalog_Model_Product_Api_TestCaseAbstract
{
    /**
     * Default helper for current test suite
     *
     * @var string
     */
    protected $_defaultHelper = 'Magento_Catalog_Model_Product_Api_Helper_Configurable';

    /**
     * Test successful configurable product create.
     * Scenario:
     * 1. Create EAV attributes and attribute set usable for configurable.
     * 2. Send request to create product with type 'configurable' and all valid attributes data.
     * Expected result:
     * Load product and assert it was created correctly.
     */
    public function testCreate()
    {
        $productData = $this->_getHelper()->getValidCreateData();
        $productId = $this->_createProductWithApi($productData);
        // Validate outcome
        /** @var $actual \Magento\Catalog\Model\Product */
        $actual = Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
        $this->_getHelper()->checkConfigurableAttributesData(
            $actual,
            $productData['configurable_attributes'],
            false
        );
        unset($productData['configurable_attributes']);
        $expected = Mage::getModel('Magento\Catalog\Model\Product');
        $expected->setData($productData);
        $this->assertProductEquals($expected, $actual);
    }
}

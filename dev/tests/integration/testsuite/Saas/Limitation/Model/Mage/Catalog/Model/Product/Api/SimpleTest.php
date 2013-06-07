<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Mage_Catalog_Model_Product_Api_SimpleTest
    extends Mage_Catalog_Model_Product_Api_TestCaseAbstract
{
    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testCreateLimitationReached()
    {
        $formattedData = $this->_prepareProductDataForSoap(
            require 'Mage/Catalog/Model/Product/Api/_files/_data/simple_product_data.php'
        );
        /** @var Saas_Limitation_Model_Catalog_Product_Limitation $limitation */
        $limitation = Mage::getModel('Saas_Limitation_Model_Catalog_Product_Limitation');
        Magento_Test_Helper_Api::callWithException($this, 'catalogProductCreate', $formattedData,
            $limitation->getCreateRestrictedMessage()
        );
    }
}

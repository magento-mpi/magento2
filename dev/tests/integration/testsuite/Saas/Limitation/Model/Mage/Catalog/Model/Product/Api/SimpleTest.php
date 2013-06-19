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
        Magento_Test_Helper_Api::callWithException($this, 'catalogProductCreate', $formattedData,
            // @codingStandardsIgnoreStart
            'Sorry, you are using all the products and variations your account allows. To add more, first delete a product or upgrade your service.'
        // @codingStandardsIgnoreEnd
        );
    }
}

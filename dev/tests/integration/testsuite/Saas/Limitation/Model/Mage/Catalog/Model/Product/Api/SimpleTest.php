<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Magento_Catalog_Model_Product_Api_SimpleTest
    extends Magento_Catalog_Model_Product_Api_TestCaseAbstract
{
    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testCreateLimitationReached()
    {
        $formattedData = $this->_prepareProductDataForSoap(
            require 'Magento/Catalog/Model/Product/Api/_files/_data/simple_product_data.php'
        );
        Magento_Test_Helper_Api::callWithException($this, 'catalogProductCreate', $formattedData,
            // @codingStandardsIgnoreStart
            'Sorry, you are using all the products and variations your account allows. To add more, first delete a product or upgrade your service.'
        // @codingStandardsIgnoreEnd
        );
    }
}

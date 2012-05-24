<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test configurable product resource as guest
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_Configurable_GuestTest extends Api2_Catalog_Products_GuestAbstract
{
    /**
     * Test successful configurable product single GET. Check received configurable attributes
     *
     * @param string $priceIncludesTax
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/catalog_price_rule.php
     * @dataProvider dataProviderForGet
     * @resourceOperation product::get
     */
    public function testGet($priceIncludesTax)
    {
        $this->_updateAppConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, 'origin', true, true, true);
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $this->assertNotEmpty($configurable->getId(), "Configurable product fixture is invalid.");
        if (Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX) != $priceIncludesTax) {
            $this->_updateAppConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $priceIncludesTax,
                true, true, true);
        }
        $restResponse = $this->callGet($this->_getResourcePath($configurable->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(),
            "Response status is invalid.");
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $fieldsMap = array('frontend_label' => 'label', 'position' => 'position');
        $this->_checkConfigurableAttributesInGet($configurable, $responseData, $fieldsMap);
    }

    /**
     * Data provider for successful configurable product get
     *
     * @return array
     */
    public function dataProviderForGet()
    {
        $priceIncludesTaxPossibleValues = array(
            array('0'),
            array('1')
        );
        return $priceIncludesTaxPossibleValues;
    }
}

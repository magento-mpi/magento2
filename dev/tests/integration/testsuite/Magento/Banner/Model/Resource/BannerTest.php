<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Banner_Model_Resource_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Banner_Model_Resource_Banner
     */
    private $_resourceModel;

    /**
     * @var int
     */
    protected $_websiteId = 1;

    /**
     * @var int
     */
    protected $_customerGroupId = Magento_Customer_Model_Group::NOT_LOGGED_IN_ID;

    protected function setUp()
    {
        $this->_resourceModel = Mage::getResourceModel('Magento_Banner_Model_Resource_Banner');
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/CatalogRule/_files/catalog_rule_10_off_not_logged.php
     * @magentoDataFixture Magento/Banner/_files/banner.php
     */
    public function testGetCatalogRuleRelatedBannerIdsNoBannerConnected()
    {
        $this->assertEmpty(
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($this->_websiteId, $this->_customerGroupId)
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Banner/_files/banner_catalog_rule.php
     */
    public function testGetCatalogRuleRelatedBannerIds()
    {
        $banner = Mage::getModel('Magento_Banner_Model_Banner');
        $banner->load('Test Banner', 'name');

        $this->assertSame(
            array($banner->getId()),
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($this->_websiteId, $this->_customerGroupId)
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Banner/_files/banner_catalog_rule.php
     * @dataProvider getCatalogRuleRelatedBannerIdsWrongDataDataProvider
     */
    public function testGetCatalogRuleRelatedBannerIdsWrongData($websiteId, $customerGroupId)
    {
        $this->assertEmpty(
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
        );
    }

    /**
     * @return array
     */
    public function getCatalogRuleRelatedBannerIdsWrongDataDataProvider()
    {
        return array(
            'wrong website' => array($this->_websiteId + 1, $this->_customerGroupId),
            'wrong customer group' => array($this->_websiteId, $this->_customerGroupId + 1)
        );
    }

    /**
     * @magentoDataFixture Magento/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Magento/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIds()
    {
        /** @var Magento_SalesRule_Model_Rule $rule */
        $rule = Mage::getModel('Magento_SalesRule_Model_Rule');
        $rule->load('40% Off on Large Orders', 'name');

        /** @var Magento_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Magento_Banner_Model_Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        $this->assertEquals(
            array($banner->getId()), $this->_resourceModel->getSalesRuleRelatedBannerIds(array($rule->getId()))
        );
    }

    /**
     * @magentoDataFixture Magento/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Magento/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIdsNoRules()
    {
        $this->assertEmpty($this->_resourceModel->getSalesRuleRelatedBannerIds(array()));
    }
}

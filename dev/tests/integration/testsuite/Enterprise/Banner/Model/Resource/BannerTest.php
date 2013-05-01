<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Model_Resource_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Model_Resource_Banner
     */
    private $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Banner');
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoDataFixture Mage/CatalogRule/_files/catalog_rule_10_off_not_logged.php
     * @magentoDataFixture Enterprise/Banner/_files/banner.php
     */
    public function testGetCatalogRuleRelatedBannerIds()
    {
        $websiteId = 1;
        $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;

        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Test Banner', 'name');

        //Banners exist
        $this->assertSame(
            array($banner->getId()),
            $this->_resourceModel->getExistingBannerIdsBySpecifiedIds(array($banner->getId()))
        );

        //There are no banners related to CatalogRules
        $this->assertEmpty($this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId));

        //Connecting CatalogRule to Banner
        $catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');
        $ruleId = $catalogRule->getCollection()->getFirstItem()->getId();

        $banner->setBannerCatalogRules(array(0 => $ruleId))->save();

        //There are banners related to CatalogRules
        $this->assertSame(
            array($banner->getId()),
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
        );

        //There are no CatalogRule banners with wrong Customer Group
        $this->assertEmpty($this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId + 1));

        //There are no CatalogRule banners with wrong $websiteId
        $this->assertEmpty($this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId + 1, $customerGroupId));
    }

    /**
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIds()
    {
        /** @var Mage_SalesRule_Model_Rule $rule */
        $rule = Mage::getModel('Mage_SalesRule_Model_Rule');
        $rule->load('40% Off on Large Orders', 'name');

        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        $this->assertEquals(
            array($banner->getId()), $this->_resourceModel->getSalesRuleRelatedBannerIds(array($rule->getId()))
        );
    }

    /**
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIdsNoRules()
    {
        $this->assertEmpty($this->_resourceModel->getSalesRuleRelatedBannerIds(array()));
    }
}

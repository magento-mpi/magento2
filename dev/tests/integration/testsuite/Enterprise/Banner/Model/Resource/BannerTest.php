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

        $bannerResource = Mage::getModel('Enterprise_Banner_Model_Resource_Banner');

        //Banners exist
        $this->assertSame(
            array($banner->getId()),
            $bannerResource->getExistingBannerIdsBySpecifiedIds(array($banner->getId()))
        );

        //There are no banners related to CatalogRules
        $this->assertEmpty($bannerResource->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId));

        //Connecting CatalogRule to Banner
        $catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');
        $ruleId = $catalogRule->getCollection()->getFirstItem()->getId();

        $banner->setBannerCatalogRules(array(0 => $ruleId))->save();

        //There are banners related to CatalogRules
        $this->assertSame(
            array($banner->getId()),
            $bannerResource->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
        );

        //There are no CatalogRule banners with wrong Customer Group
        $this->assertEmpty($bannerResource->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId + 1));

        //There are no CatalogRule banners with wrong $websiteId
        $this->assertEmpty($bannerResource->getCatalogRuleRelatedBannerIds($websiteId + 1, $customerGroupId));
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Catalog/_files/product_simple.php
 * @magentoDataFixture Mage/CatalogRule/_files/catalog_rule_10_off_not_logged.php
 * @magentoDataFixture Enterprise/Banner/_files/banner_catalog_rule.php
 */
class Enterprise_Banner_Model_Resource_Catalogrule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    protected $_collection;

    /**
     * @var Enterprise_Banner_Model_Banner
     */
    protected $_banner;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Catalogrule_Collection');
        $this->_banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $this->_banner->load('Test Banner', 'name');
    }

    protected function tearDown()
    {
        $this->_collection = null;
        $this->_banner = null;
    }

    public function testConstructor()
    {
        $this->assertSame('enterprise_banner_catalogrule', $this->_collection->getMainTable());
    }

    public function testBannerCatalogrule()
    {
        $firstItem = $this->_collection->getFirstItem();
        $this->assertEquals($this->_banner->getId(), $firstItem->getBannerId());
    }

    public function testAddWebsiteCustomerGroupFilter()
    {
        $websiteId = 1;
        $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;

        $firstItem = $this->_collection->addWebsiteCustomerGroupFilter($websiteId, $customerGroupId)->getFirstItem();

        $this->assertEquals($this->_banner->getId(), $firstItem->getBannerId());
    }
}

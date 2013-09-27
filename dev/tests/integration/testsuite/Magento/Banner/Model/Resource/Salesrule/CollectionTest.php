<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Banner/_files/banner_enabled_40_to_50_percent_off.php
 * @magentoDataFixture Magento/Banner/_files/banner_disabled_40_percent_off.php
 */
class Magento_Banner_Model_Resource_Salesrule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Banner_Model_Resource_Salesrule_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Banner_Model_Resource_Salesrule_Collection');
    }

    protected function tearDown()
    {
        $this->_collection = null;
    }

    public function testGetItems()
    {
        /** @var Magento_Banner_Model_Banner $banner */
        $banner = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Banner_Model_Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        $this->assertCount(1, $this->_collection->getItems());
        $this->assertEquals($banner->getId(), $this->_collection->getFirstItem()->getData('banner_id'));
    }

    public function testAddRuleIdsFilter()
    {
        /** @var Magento_SalesRule_Model_Rule $rule */
        $rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_SalesRule_Model_Rule');
        $rule->load('40% Off on Large Orders', 'name');

        $this->_collection->addRuleIdsFilter(array($rule->getId()));

        $this->testGetItems();
    }

    public function testAddRuleIdsFilterNoRules()
    {
        $this->_collection->addRuleIdsFilter(array());

        $this->assertEmpty($this->_collection->getItems());
    }
}

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
namespace Magento\Banner\Model\Resource\Salesrule;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Model\Resource\Salesrule\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Mage::getResourceModel('Magento\Banner\Model\Resource\Salesrule\Collection');
    }

    protected function tearDown()
    {
        $this->_collection = null;
    }

    public function testGetItems()
    {
        /** @var \Magento\Banner\Model\Banner $banner */
        $banner = \Mage::getModel('Magento\Banner\Model\Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        $this->assertCount(1, $this->_collection->getItems());
        $this->assertEquals($banner->getId(), $this->_collection->getFirstItem()->getData('banner_id'));
    }

    public function testAddRuleIdsFilter()
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = \Mage::getModel('Magento\SalesRule\Model\Rule');
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

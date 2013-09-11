<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Store_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Store\Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento\Core\Model\Resource\Store\Collection');
    }

    public function testSetGetLoadDefault()
    {
        $this->assertFalse($this->_collection->getLoadDefault());

        $this->_collection->setLoadDefault(true);
        $this->assertTrue($this->_collection->getLoadDefault());

        $this->_collection->setLoadDefault(false);
        $this->assertFalse($this->_collection->getLoadDefault());
    }

    public function testSetWithoutDefaultFilter()
    {
        $this->_collection->setWithoutDefaultFilter();
        $quote = $this->_getQuoteIdentifierSymbol();

        $this->assertContains("{$quote}store_id{$quote} > 0", (string)$this->_collection->getSelect());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Store\Collection::addGroupFilter
     * @covers \Magento\Core\Model\Resource\Store\Collection::addIdFilter
     * @covers \Magento\Core\Model\Resource\Store\Collection::addWebsiteFilter
     * @covers \Magento\Core\Model\Resource\Store\Collection::addCategoryFilter
     */
    public function testAddFilters()
    {
        $this->_collection->addGroupFilter(1);
        $quote = $this->_getQuoteIdentifierSymbol();
        $this->assertContains("{$quote}group_id{$quote} IN", (string)$this->_collection->getSelect(), 'Group filter');

        $this->_collection->addIdFilter(1);
        $this->assertContains("{$quote}store_id{$quote} IN", (string)$this->_collection->getSelect(), 'Id filter');

        $this->_collection->addWebsiteFilter(1);
        $this->assertContains(
            "{$quote}website_id{$quote} IN",
            (string)$this->_collection->getSelect(),
            'Website filter'
        );

        $this->_collection->addCategoryFilter(1);
        $this->assertContains(
            "{$quote}root_category_id{$quote} IN",
            (string)$this->_collection->getSelect(),
            'Category filter'
        );
    }

    /**
     * Get quote symbol from adapter.
     *
     * @return string
     */
    protected function _getQuoteIdentifierSymbol()
    {
        /** @var Zend_Db_Adapter_Abstract $adapter */
        $adapter = $this->_collection->getConnection();
        $quote = $adapter->getQuoteIdentifierSymbol();
        return $quote;
    }

    public function testToOptionArrayHash()
    {
        $this->assertTrue(is_array($this->_collection->toOptionArray()));
        $this->assertNotEmpty($this->_collection->toOptionArray());

        $this->assertTrue(is_array($this->_collection->toOptionHash()));
        $this->assertNotEmpty($this->_collection->toOptionHash());

    }

    /**
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::addFieldToSelect
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::removeFieldFromSelect
     */
    public function testAddRemoveFieldToSelect()
    {
        $this->_collection->addFieldToSelect(array('store_id'));
        $this->assertContains('store_id', (string)$this->_collection->getSelect());
        $this->_collection->addFieldToSelect('*');
        $this->assertContains('*', (string)$this->_collection->getSelect());

        $this->_collection->addFieldToSelect('test_field', 'test_alias');
        $this->assertContains('test_field', (string)$this->_collection->getSelect());
        $this->assertContains('test_alias', (string)$this->_collection->getSelect());

        $this->_collection->removeFieldFromSelect('test_field');
        $this->_collection->addFieldToSelect('store_id');
        $this->assertNotContains('test_field', (string)$this->_collection->getSelect());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::addExpressionFieldToSelect
     */
    public function testAddExpressionFieldToSelect()
    {
        $this->_collection->addExpressionFieldToSelect('test_alias', 'SUM({{store_id}})', 'store_id');
        $this->assertContains('SUM(store_id)', (string)$this->_collection->getSelect());
        $this->assertContains('test_alias', (string)$this->_collection->getSelect());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::getAllIds
     */
    public function testGetAllIds()
    {
        $this->assertContains(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID, $this->_collection->getAllIds());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::getData
     */
    public function testGetData()
    {
        $this->assertNotEmpty($this->_collection->getData());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Db\Collection\AbstractCollection::join
     */
    public function testJoin()
    {
        $this->_collection->join(array('w' => 'core_website'), 'main_table.website_id=w.website_id');
        $this->assertContains('core_website', (string)$this->_collection->getSelect());
    }
}

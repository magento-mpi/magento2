<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Layout_Update_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected $_collection;

    /**
     * Retrieve layout update collection instance
     *
     * @param Zend_Db_Select $select
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _getCollection(Zend_Db_Select $select)
    {
        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array('select'), '', false);
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));

        $resource = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract', array(), '', false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable'));
        $resource->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));

        $this->_collection = new Mage_Core_Model_Resource_Layout_Update_Collection($resource);
        return $this->_collection;
    }

    protected function tearDown()
    {
        unset($this->_collection);
    }

    public function testAddThemeFilter()
    {
        $themeId = 1;
        $select = $this->getMock('Zend_Db_Select', array(), array('where'), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with('link.theme_id = ?', $themeId);

        $collection = $this->_getCollection($select);
        $collection->addThemeFilter($themeId);
    }

    public function testAddStoreFilter()
    {
        $storeId = 1;
        $select = $this->getMock('Zend_Db_Select', array(), array('where'), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with('link.store_id = ?', $storeId);

        $collection = $this->_getCollection($select);
        $collection->addStoreFilter($storeId);
    }

    /**
     * @covers Mage_Core_Model_Resource_Layout_Update_Collection::_joinWithLink
     */
    public function testJoinWithLink()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array('where'), '', false);
        $select->expects($this->once())
            ->method('join')
            ->with(
                array('link' => 'core_layout_link'),
                'link.layout_update_id = main_table.layout_update_id',
                $this->isType('array')
            );

        $collection = $this->_getCollection($select);
        $collection->addStoreFilter(1);
        $collection->addThemeFilter(1);
    }
}

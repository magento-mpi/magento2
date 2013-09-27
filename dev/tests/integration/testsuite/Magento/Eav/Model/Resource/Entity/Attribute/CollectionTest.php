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

class Magento_Eav_Model_Resource_Entity_Attribute_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Resource_Entity_Attribute_Collection');
    }

    /**
     * Returns array of set ids, present in collection attributes
     *
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return array
     */
    protected function _getSets($collection)
    {
        $collection->addSetInfo();

        $sets = array();
        foreach ($collection as $attribute) {
            foreach (array_keys($attribute->getAttributeSetInfo()) as $setId) {
                $sets[$setId] = $setId;
            }
        }
        return array_values($sets);
    }

    public function testSetAttributeGroupFilter()
    {
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Resource_Entity_Attribute_Collection');
        $groupsPresent = $this->_getGroups($collection);
        $includeGroupId = current($groupsPresent);

        $this->_model->setAttributeGroupFilter($includeGroupId);
        $groups = $this->_getGroups($this->_model);

        $this->assertEquals(array($includeGroupId), $groups);
    }

    /**
     * Returns array of group ids, present in collection attributes
     *
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return array
     */
    protected function _getGroups($collection)
    {
        $collection->addSetInfo();

        $groups = array();
        foreach ($collection as $attribute) {
            foreach ($attribute->getAttributeSetInfo() as $setInfo) {
                $groupId = $setInfo['group_id'];
                $groups[$groupId] = $groupId;
            }
        }
        return array_values($groups);
    }

    public function testAddAttributeGrouping()
    {
        $select = $this->_model->getSelect();
        $this->assertEmpty($select->getPart(Zend_Db_Select::GROUP));
        $this->_model->addAttributeGrouping();
        $this->assertEquals(array('main_table.attribute_id'), $select->getPart(Zend_Db_Select::GROUP));
    }
}

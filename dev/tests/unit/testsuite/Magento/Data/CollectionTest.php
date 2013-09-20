<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Data_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Data_Collection
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Magento_Data_Collection(
            $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false)
        );
    }

    public function testRemoveAllItems()
    {
        $this->_model->addItem(new Magento_Object());
        $this->_model->addItem(new Magento_Object());
        $this->assertCount(2, $this->_model->getItems());
        $this->_model->removeAllItems();
        $this->assertEmpty($this->_model->getItems());
    }

    /**
     * @dataProvider setItemObjectClassDataProvider
     */
    public function testSetItemObjectClass($class)
    {
        $this->_model->setItemObjectClass($class);
        $this->assertAttributeSame($class, '_itemObjectClass', $this->_model);
    }

    /**
     * @return array
     */
    public function setItemObjectClassDataProvider()
    {
        return array(
            array('Magento_Core_Model_Url'),
            array('Magento_Object'),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Incorrect_ClassName does not extend Magento_Object
     */
    public function testSetItemObjectClassException()
    {
        $this->_model->setItemObjectClass('Incorrect_ClassName');
    }
}

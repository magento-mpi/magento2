<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Data\Collection
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Data\Collection(
            $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false)
        );
    }

    public function testRemoveAllItems()
    {
        $this->_model->addItem(new \Magento\Object());
        $this->_model->addItem(new \Magento\Object());
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
            array('Magento\Core\Model\Url'),
            array('Magento\Object'),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Incorrect_ClassName does not extend \Magento\Object
     */
    public function testSetItemObjectClassException()
    {
        $this->_model->setItemObjectClass('Incorrect_ClassName');
    }
}

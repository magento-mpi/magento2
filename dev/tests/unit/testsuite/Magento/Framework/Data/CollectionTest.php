<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Framework\Data\Collection(
            $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false)
        );
    }

    public function testRemoveAllItems()
    {
        $this->_model->addItem(new \Magento\Framework\Object());
        $this->_model->addItem(new \Magento\Framework\Object());
        $this->assertCount(2, $this->_model->getItems());
        $this->_model->removeAllItems();
        $this->assertEmpty($this->_model->getItems());
    }

    /**
     * Test loadWithFilter()
     * @return void
     */
    public function testLoadWithFilter()
    {
        $this->assertInstanceOf('Magento\Framework\Data\Collection', $this->_model->loadWithFilter());
        $this->assertEmpty($this->_model->getItems());
        $this->_model->addItem(new \Magento\Framework\Object());
        $this->_model->addItem(new \Magento\Framework\Object());
        $this->assertCount(2, $this->_model->loadWithFilter()->getItems());
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
        return array(array('Magento\Framework\Url'), array('Magento\Framework\Object'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Incorrect_ClassName does not extend \Magento\Framework\Object
     */
    public function testSetItemObjectClassException()
    {
        $this->_model->setItemObjectClass('Incorrect_ClassName');
    }
}

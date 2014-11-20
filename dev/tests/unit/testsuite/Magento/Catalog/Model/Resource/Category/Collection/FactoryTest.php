<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Category\Collection;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\Collection\Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_model = new \Magento\Catalog\Model\Resource\Category\Collection\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $objectOne = $this->getMock('Magento\Catalog\Model\Resource\Category\Collection', array(), array(), '', false);
        $objectTwo = $this->getMock('Magento\Catalog\Model\Resource\Category\Collection', array(), array(), '', false);
        $this->_objectManager->expects(
            $this->exactly(2)
        )->method(
            'create'
        )->with(
            'Magento\Catalog\Model\Resource\Category\Collection',
            array()
        )->will(
            $this->onConsecutiveCalls($objectOne, $objectTwo)
        );
        $this->assertSame($objectOne, $this->_model->create());
        $this->assertSame($objectTwo, $this->_model->create());
    }
}

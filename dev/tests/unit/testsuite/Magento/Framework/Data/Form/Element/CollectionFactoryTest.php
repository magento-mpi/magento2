<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\CollectionFactory
 */
namespace Magento\Framework\Data\Form\Element;

class CollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerMock = $this->getMock(
            'Magento\ObjectManager\ObjectManager',
            array('create'),
            array(),
            '',
            false
        );
        $collectionMock = $this->getMock('Magento\Framework\Data\Form\Element\Collection', array(), array(), '', false);
        $objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($collectionMock));
        $this->_model = new \Magento\Framework\Data\Form\Element\CollectionFactory($objectManagerMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\CollectionFactory::create
     */
    public function testCreate()
    {
        $this->assertInstanceOf('Magento\Framework\Data\Form\Element\Collection', $this->_model->create(array()));
    }
}

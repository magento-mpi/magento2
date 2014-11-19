<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Element;

class FlyweightFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Element\FlyweightFactory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_model = new \Magento\Backend\Model\Config\Structure\Element\FlyweightFactory(
            $this->_objectManagerMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    public function testCreate()
    {
        $this->_objectManagerMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValueMap(
                array(
                    array('Magento\Backend\Model\Config\Structure\Element\Section', array(), 'sectionObject'),
                    array('Magento\Backend\Model\Config\Structure\Element\Group', array(), 'groupObject'),
                    array('Magento\Backend\Model\Config\Structure\Element\Field', array(), 'fieldObject')
                )
            )
        );
        $this->assertEquals('sectionObject', $this->_model->create('section'));
        $this->assertEquals('groupObject', $this->_model->create('group'));
        $this->assertEquals('fieldObject', $this->_model->create('field'));
    }
}

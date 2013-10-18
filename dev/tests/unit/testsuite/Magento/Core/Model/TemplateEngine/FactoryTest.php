<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\TemplateEngine;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_dynamicBehavior = $this->getMock(
            'Magento\Core\Model\TemplateEngine\DynamicBehavior', array(), array(), '', false
        );
        $this->_model = new Factory($this->_objectManager, array(
            'test' => 'Fixture\Module\Model\TemplateEngine',
        ));
    }

    public function testCreateKnownEngine()
    {
        $engine = $this->getMock('Magento\Core\Model\TemplateEngine\EngineInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Fixture\Module\Model\TemplateEngine')
            ->will($this->returnValue($engine))
        ;
        $this->assertSame($engine, $this->_model->create('test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown template engine 'non_existing'
     */
    public function testCreateUnknownEngine()
    {
        $this->_objectManager->expects($this->never())->method('create');
        $this->_model->create('non_existing');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Fixture\Module\Model\TemplateEngine has to implement the template engine interface
     */
    public function testCreateInvalidEngine()
    {
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Fixture\Module\Model\TemplateEngine')
            ->will($this->returnValue(new \stdClass()))
        ;
        $this->_model->create('test');
    }
}

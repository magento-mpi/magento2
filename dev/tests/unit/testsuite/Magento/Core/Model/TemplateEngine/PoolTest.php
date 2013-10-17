<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\TemplateEngine;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\TemplateEngine\Pool
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\TemplateEngine\Pool($this->_objectManager, array(
            'test' => 'Fixture\Module\Model\TemplateEngine',
        ));
    }

    public function testGetKnownEngine()
    {
        $engine = $this->getMock('\Magento\Core\Model\TemplateEngine\EngineInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Fixture\Module\Model\TemplateEngine'))
            ->will($this->returnValue($engine))
        ;
        $this->assertSame($engine, $this->_model->get('test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown template engine 'non_existing'
     */
    public function testGetUnknownEngine()
    {
        $this->_objectManager->expects($this->never())->method('get');
        $this->_model->get('non_existing');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Fixture\Module\Model\TemplateEngine has to implement the template engine interface
     */
    public function testGetInvalidEngine()
    {
        $this->_objectManager
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Fixture\Module\Model\TemplateEngine'))
            ->will($this->returnValue(new \stdClass()))
        ;
        $this->_model->get('test');
    }
}

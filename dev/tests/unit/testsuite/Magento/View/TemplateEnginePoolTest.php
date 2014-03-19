<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View;

class TemplateEnginePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateEnginePool
     */
    protected $_model;

    /**
     * @var\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_factory = $this->getMock('Magento\View\TemplateEngineFactory', array(), array(), '', false);
        $this->_model = new TemplateEnginePool($this->_factory);
    }

    public function testGet()
    {
        $engine = $this->getMock('Magento\View\TemplateEngineInterface');
        $this->_factory->expects($this->once())->method('create')->with('test')->will($this->returnValue($engine));
        $this->assertSame($engine, $this->_model->get('test'));
        // Make sure factory is invoked only once and the same instance is returned afterwards
        $this->assertSame($engine, $this->_model->get('test'));
    }
}

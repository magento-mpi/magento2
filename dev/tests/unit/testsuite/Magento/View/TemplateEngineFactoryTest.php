<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class TemplateEngineFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var  \Magento\View\TemplateEngineFactory */
    protected $_factory;

    /**
     * Setup a factory to test with an mocked object manager.
     */
    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_factory = new TemplateEngineFactory($this->_objectManagerMock);
    }

    public function testCreateKnownEngine()
    {
        $engine = $this->getMock('Magento\View\TemplateEngineInterface');
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->with('Magento\View\TemplateEngine\Php')
            ->will($this->returnValue($engine))
        ;
        $this->assertSame($engine, $this->_factory->get('phtml'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown template engine type: non_existing
     */
    public function testCreateUnknownEngine()
    {
        $this->_objectManagerMock->expects($this->never())->method('get');
        $this->_factory->get('non_existing');
    }
}

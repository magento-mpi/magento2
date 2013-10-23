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

    /**
     * Test getting a phtml engine
     */
    public function testGetPhtmlEngine()
    {
        $phtmlEngineMock = $this->getMock('Magento\View\TemplateEngine\Php');
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento\View\TemplateEngine\Php'))
            ->will($this->returnValue($phtmlEngineMock));
        $actual = $this->_factory->get(TemplateEngineFactory::ENGINE_PHTML);
        $this->assertSame($phtmlEngineMock, $actual, 'phtml engine not returned');
    }

    /**
     * Test attempting to get an engine the factory does not know about (neither Twig nor Phtml.)
     *
     * Should throw an exception
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown template engine type: NotAnEngineName
     */
    public function testGetBadEngine()
    {
        $this->_objectManagerMock->expects($this->never())
            ->method('get');
        $this->_factory->get('NotAnEngineName');
    }

    /**
     * Test attempting to get an engine passing in null as the engine type.
     *
     * Should throw an exception
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown template engine type:
     */
    public function testGetNullEngine()
    {
        $this->_objectManagerMock->expects($this->never())
            ->method('get');
        $this->_factory->get(NULL);
    }
}

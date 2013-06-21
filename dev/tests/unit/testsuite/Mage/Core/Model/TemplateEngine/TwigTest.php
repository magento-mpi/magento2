<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_TwigTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Core_Model_TemplateEngine_Twig */
    protected $_twigEngine;

    /** @var  PHPUnit_Framework_MockObject_MockObject Mage_Core_Model_TemplateEngine_EnvironmentFactory */
    protected $_envFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_extMock;

    /**
     * Create a Twig template engine to test.
     */
    public function setUp()
    {
        // Objects that are injected into Mage_Core_Model_TemplateEngine_Twig
        $this->_envFactoryMock = $this->getMockBuilder('Mage_Core_Model_TemplateEngine_Twig_EnvironmentFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_extMock = $this->getMockBuilder('Mage_Core_Model_TemplateEngine_Twig_Extension')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_twigEngine
            = new Mage_Core_Model_TemplateEngine_Twig($this->_envFactoryMock, $this->_extMock);
    }

    /**
     * Test the render() function with a very simple .twig file.
     */
    public function testRenderPositive()
    {
        $renderedOutput = '<html></html>';
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Template')
            ->disableOriginalConstructor()->getMock();
        $environmentMock = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $environmentMock->expects($this->once())->method('render')->will($this->returnValue($renderedOutput));
        $this->_envFactoryMock->expects($this->once())->method('create')->will(
            $this->returnValue($environmentMock)
        );
        $actualOutput = $this->_twigEngine->render($blockMock, '');
        $this->assertSame($renderedOutput, $actualOutput, 'Twig file did not render properly');
    }

    /**
     * Test the render() function such that it throws an exception
     * 
     * @expectedException Exception
     */    
    public function testRenderNegative() 
    {
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Template')
        ->disableOriginalConstructor()->getMock();
        $environmentMock = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()->getMock();
        $environmentMock->expects($this->once())
            ->method('render')
            ->will($this->throwException(new Exception()));
        $this->_envFactoryMock->expects($this->once())->method('create')->will(
                $this->returnValue($environmentMock)
        );
        $this->_twigEngine->render($blockMock, '');
    }
    
    /**
     * Test the getCurrentBlock function.
     *
     * Since its set/reset during render(), make sure it does not return anything when empty.
     */
    public function testGetCurrentBlock()
    {
        $block = $this->_twigEngine->getCurrentBlock();
        $this->assertNull($block);
    }
}

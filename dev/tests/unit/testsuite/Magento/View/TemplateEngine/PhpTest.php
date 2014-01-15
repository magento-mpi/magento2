<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\TemplateEngine;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    const TEST_PROP_VALUE = 'TEST_PROP_VALUE';

    /** @var  \Magento\View\TemplateEngine\Php */
    protected $_phpEngine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactoryMock;

    /**
     * Create a PHP template engine to test.
     */
    protected function setUp()
    {
        $this->_helperFactoryMock = $this->getMock('Magento\ObjectManager');
        $this->_phpEngine = new \Magento\View\TemplateEngine\Php($this->_helperFactoryMock);
    }

    /**
     * Test the render() function with a very simple .phtml file.
     *
     * Note: the call() function will be covered because simple.phtml has a call to the block.
     */
    public function testRender()
    {
        $blockMock = $this->getMockBuilder('Magento\View\Element\Template')
            ->setMethods(array('testMethod'))
            ->disableOriginalConstructor()->getMock();

        $blockMock->expects($this->once())->method('testMethod');
        $blockMock->property = self::TEST_PROP_VALUE;

        $filename = __DIR__ . '/_files/simple.phtml';
        $actualOutput = $this->_phpEngine->render($blockMock, $filename);

        $this->assertAttributeEquals(null, '_currentBlock', $this->_phpEngine);

        $expectedOutput = '<html>'.self::TEST_PROP_VALUE.'</html>';
        $this->assertSame($expectedOutput, $actualOutput, 'phtml file did not render correctly');
    }

    /**
     * Test the render() function with a nonexistent filename.
     *
     * Expect an exception if the specified file does not exist.
     * We should really expect a generic \Exception, but PHPUnit will fail
     * with: "You must not expect the generic exception class".  This has been fixed in more recent versions of
     * PHPUnit, but until all build agents get updated with PHPUnit 3.7.20, the workaround is
     * to specify \PHPUnit_Framework_Error_Warning
     * @expectedException \PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage include(This_is_not_a_file): failed to open stream: No such file or directory
     */
    public function testRenderException()
    {
        $blockMock = $this->getMockBuilder('Magento\View\Element\Template')
            ->setMethods(array('testMethod'))
            ->disableOriginalConstructor()->getMock();

        $filename = 'This_is_not_a_file';
        $this->_phpEngine->render($blockMock, $filename);
    }

    /**
     * @expectedException \LogicException
     */
    public function testHelperWithInvalidClass()
    {
        $class = 'Magento\Object';
        $object = $this->getMock($class, array(), array(), '', false);
        $this->_helperFactoryMock->expects($this->once())
            ->method('get')->with($class)->will($this->returnValue($object));
        $this->_phpEngine->helper($class);
    }

    public function testHelperWithValidClass()
    {
        $class = 'Magento\App\Helper\AbstractHelper';
        $object = $this->getMockForAbstractClass($class, array(), '', false);
        $this->_helperFactoryMock->expects($this->once())
            ->method('get')->with($class)->will($this->returnValue($object));
        $this->assertEquals($object, $this->_phpEngine->helper($class));
    }


}

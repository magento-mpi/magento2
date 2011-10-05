<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Profiler
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Profiler_Output_Html
 *
 * @group profiler
 */
class Magento_Profiler_Output_HtmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Output_Html|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    public static function setUpBeforeClass()
    {
        Magento_Profiler::enable();
        /* Profiler measurements fixture */
        $timersProperty = new ReflectionProperty('Magento_Profiler', '_timers');
        $timersProperty->setAccessible(true);
        $timersProperty->setValue(include __DIR__ . '/../_files/timers.php');
        $timersProperty->setAccessible(false);
    }

    public static function tearDownAfterClass()
    {
        Magento_Profiler::reset();
    }

    protected function setUp()
    {
        $this->_object = $this->getMock('Magento_Profiler_Output_Html', array('_renderCaption'));
        $this->_object
            ->expects($this->any())
            ->method('_renderCaption')
            ->will($this->returnValue('Code Profiler Title'))
        ;
    }

    public function testDisplay()
    {
        ob_start();
        $this->_object->display();
        $actualHtml = ob_get_clean();
        $expectedHtmlFile = __DIR__ . '/../_files/output.html';
        $this->assertStringEqualsFile($expectedHtmlFile, $actualHtml);
    }
}

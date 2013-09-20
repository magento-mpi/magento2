<?php
/**
 * Test case for \Magento\Profiler\Driver\Standard\Output\Html
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler\Driver\Standard\Output;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Profiler\Driver\Standard\Output\Html
     */
    protected $_output;

    protected function setUp()
    {
        $this->_output = new \Magento\Profiler\Driver\Standard\Output\Html();
    }

    /**
     * Test display method
     *
     * @dataProvider displayDataProvider
     * @param string $statFile
     * @param string $expectedHtmlFile
     */
    public function testDisplay($statFile, $expectedHtmlFile)
    {
        $stat = include $statFile;
        $expectedHtml = file_get_contents($expectedHtmlFile);

        ob_start();
        $this->_output->display($stat);
        $actualHtml = ob_get_clean();

        $this->_assertDisplayResultEquals($actualHtml, $expectedHtml);
    }

    /**
     * @return array
     */
    public function displayDataProvider()
    {
        return array(
            array('statFile' => __DIR__ . '/_files/timers.php', 'expectedHtmlFile' => __DIR__ . '/_files/output.html')
        );
    }

    /**
     * Asserts display() result equals
     *
     * @param string $actualHtml
     * @param string $expectedHtml
     */
    protected function _assertDisplayResultEquals($actualHtml, $expectedHtml)
    {
        $expectedHtml = ltrim(preg_replace('/^<!--.+?-->/s', '', $expectedHtml));
        if (preg_match('/Code Profiler \(Memory usage: real - (\d+), emalloc - (\d+)\)/', $actualHtml, $matches)) {
            list(, $realMemory, $emallocMemory) = $matches;
            $expectedHtml = str_replace(
                array('%real_memory%', '%emalloc_memory%'),
                array($realMemory, $emallocMemory),
                $expectedHtml
            );
        }
        $this->assertEquals($expectedHtml, $actualHtml);

    }
}

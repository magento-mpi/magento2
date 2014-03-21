<?php
/**
 * Test case for \Magento\Profiler\Driver\Standard\Output\Csvfile
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler\Driver\Standard\Output;

class CsvfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Profiler\Driver\Standard\Output\Csvfile
     */
    protected $_output;

    /**
     * @var string
     */
    protected $_outputFile;

    protected function setUp()
    {
        $this->_outputFile = tempnam(sys_get_temp_dir(), __CLASS__);
    }

    /**
     * Test display method
     *
     * @dataProvider displayDataProvider
     * @param string $statFile
     * @param string $expectedFile
     * @param string $delimiter
     * @param string $enclosure
     */
    public function testDisplay($statFile, $expectedFile, $delimiter = ',', $enclosure = '"')
    {
        $this->_output = new \Magento\Profiler\Driver\Standard\Output\Csvfile(
            array('filePath' => $this->_outputFile, 'delimiter' => $delimiter, 'enclosure' => $enclosure)
        );
        $stat = include $statFile;
        $this->_output->display($stat);
        $this->assertFileEquals($expectedFile, $this->_outputFile);
    }

    /**
     * @return array
     */
    public function displayDataProvider()
    {
        return array(
            'Default delimiter & enclosure' => array(
                'statFile' => __DIR__ . '/_files/timers.php',
                'expectedHtmlFile' => __DIR__ . '/_files/output_default.csv'
            ),
            'Custom delimiter & enclosure' => array(
                'statFile' => __DIR__ . '/_files/timers.php',
                'expectedHtmlFile' => __DIR__ . '/_files/output_custom.csv',
                '.',
                '`'
            )
        );
    }
}

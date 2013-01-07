<?php
/**
 * Test case for Magento_Profiler_Driver_Standard_Output_Csvfile
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_CsvfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_Output_Csvfile
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
        $this->_output = new Magento_Profiler_Driver_Standard_Output_Csvfile(array(
            'filePath' => $this->_outputFile,
            'delimiter' => $delimiter,
            'enclosure' => $enclosure
        ));
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
            ),
        );
    }
}

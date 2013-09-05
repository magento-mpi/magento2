<?php
/**
 * Test class for \Magento\Profiler\Driver\Standard\Output\Csvfile
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_CsvfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructorProvider
     * @param array $config
     * @param string $expectedFilePath
     * @param string $expectedDelimiter
     * @param string $expectedEnclosure
     */
    public function testConstructor($config, $expectedFilePath, $expectedDelimiter, $expectedEnclosure)
    {
        $output = new \Magento\Profiler\Driver\Standard\Output\Csvfile($config);
        $this->assertAttributeEquals($expectedFilePath, '_filePath', $output);
        $this->assertAttributeEquals($expectedDelimiter, '_delimiter', $output);
        $this->assertAttributeEquals($expectedEnclosure, '_enclosure', $output);
    }

    /**
     * @return array
     */
    public function constructorProvider()
    {
        return array(
            'Default config' => array(
                'config' => array(),
                'filePath' => '/var/log/profiler.csv',
                'delimiter' => ',',
                'enclosure' => '"'
            ),
            'Custom config' => array(
                'config' => array(
                    'baseDir' => '/var/www/project/',
                    'filePath' => '/log/example.csv',
                    'delimiter' => "\t",
                    'enclosure' => '"'
                ),
                'filePath' => '/var/www/project/log/example.csv',
                'delimiter' => "\t",
                'enclosure' => '"'
            ),
        );
    }
}

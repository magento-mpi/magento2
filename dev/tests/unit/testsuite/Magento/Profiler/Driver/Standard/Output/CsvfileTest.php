<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_Output_Csvfile
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
     * @param Magento_Profiler_Driver_Standard_Output_Configuration $config
     * @param string $expectedFilePath
     * @param string $expectedDelimiter
     * @param string $expectedEnclosure
     */
    public function testConstructor($config, $expectedFilePath, $expectedDelimiter, $expectedEnclosure)
    {
        $output = new Magento_Profiler_Driver_Standard_Output_Csvfile($config);
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
                'config' => new Magento_Profiler_Driver_Standard_Output_Configuration(),
                'filePath' => str_replace('/', DIRECTORY_SEPARATOR, '/var/log/profiler.csv'),
                'delimiter' => ',',
                'enclosure' => '"'
            ),
            'Custom config' => array(
                'config' => new Magento_Profiler_Driver_Standard_Output_Configuration(array(
                    'baseDir' => str_replace('/', DIRECTORY_SEPARATOR, '/var/www/project/'),
                    'filePath' => str_replace('/', DIRECTORY_SEPARATOR, '/log/example.csv'),
                    'delimiter' => "\t",
                    'enclosure' => '"'
                )),
                'filePath' => str_replace('/', DIRECTORY_SEPARATOR, '/var/www/project/log/example.csv'),
                'delimiter' => "\t",
                'enclosure' => '"'
            ),
        );
    }
}

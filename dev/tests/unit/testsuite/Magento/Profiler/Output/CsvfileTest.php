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
 * Test case for Magento_Profiler_Output_Csvfile
 *
 * @group profiler
 */
class Magento_Profiler_Output_CsvfileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Output_Csvfile
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_actualCsvFile;

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
        do {
            $this->_actualCsvFile = Magento_Test_Environment::getInstance()->getTmpDir()
                . DIRECTORY_SEPARATOR . md5(time() + microtime(true));
        } while (file_exists($this->_actualCsvFile));
    }

    /**
     * @dataProvider displayDataProvider
     */
    public function testDisplay($delimiter, $enclosure, $expectedCsvFile)
    {
        $this->_object = new Magento_Profiler_Output_Csvfile($this->_actualCsvFile, null, $delimiter, $enclosure);
        $this->_object->display();

        $this->assertFileEquals($expectedCsvFile, $this->_actualCsvFile);
    }

    public function displayDataProvider()
    {
        return array(
            'default delimiter & enclosure' => array(',', '"', __DIR__ . '/../_files/output_default.csv'),
            'custom delimiter & enclosure'  => array('.', '`', __DIR__ . '/../_files/output_custom.csv'),
        );
    }

    public function testDisplayDefaults()
    {
        $this->_object = new Magento_Profiler_Output_Csvfile($this->_actualCsvFile);
        $this->_object->display();

        $expectedCsvFile = __DIR__ . '/../_files/output_default.csv';
        $this->assertFileEquals($expectedCsvFile, $this->_actualCsvFile);
    }
}

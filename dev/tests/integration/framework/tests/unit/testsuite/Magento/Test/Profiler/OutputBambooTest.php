<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/OutputBambooTestFilter.php';
/**
 * Test class for Magento_TestFramework_Profiler_OutputBamboo.
 */
class Magento_Test_Profiler_OutputBambooTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Profiler_OutputBamboo
     */
    protected $_output;

    public static function setUpBeforeClass()
    {
        stream_filter_register('dataCollectorFilter', 'Magento_Test_Profiler_OutputBambooTestFilter');
    }

    /**
     * Reset collected data and prescribe to pass stream data through the collector filter
     */
    protected function setUp()
    {
        Magento_Test_Profiler_OutputBambooTestFilter::resetCollectedData();

        /**
         * @link http://php.net/manual/en/wrappers.php.php
         */
        $this->_output = new Magento_TestFramework_Profiler_OutputBamboo(array(
            'filePath' => 'php://filter/write=dataCollectorFilter/resource=php://memory',
            'metrics' => array('sample metric (ms)' => array('profiler_key_for_sample_metric'))
        ));
    }

    public function testDisplay()
    {
        $this->_output->display(new \Magento\Profiler\Driver\Standard\Stat());
        Magento_Test_Profiler_OutputBambooTestFilter::assertCollectedData("Timestamp,\"sample metric (ms)\"\n%d,%d");
    }
}

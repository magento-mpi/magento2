<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Test_Tools_Di_Code_Scanner_XmlInterceptorScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\XmlInterceptorScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var array
     */
    protected $_testFiles = array();

    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\XmlInterceptorScanner();
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
        $this->_testFiles =  array(
            $this->_testDir . '/app/code/Magento/SomeModule/etc/di.xml',
            $this->_testDir . '/app/etc/di/config.xml',
        );
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Magento\Core\Model\Cache\Interceptor',
            'Magento\Core\Controller\Varien\Action\Context\Interceptor',
        );
        $this->assertEquals($expected, $actual);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Test\Tools\Di\Code\Scanner;

class DirectoryScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Di\Code\Scanner\DirectoryScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Di\Code\Scanner\DirectoryScanner();
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
    }

    public function testScan()
    {
        $filePatterns = array(
            'php' => '/.*\.php$/',
            'etc' => '/\/app\/etc\/.*\.xml$/',
            'config' => '/\/etc\/(config([a-z0-9\.]*)?|adminhtml\/system)\.xml$/',
            'view' => '/\/view\/[a-z0-9A-Z\/\.]*\.xml$/',
            'design' => '/\/app\/design\/[a-z0-9A-Z\/\.]*\.xml$/'
        );

        $actual = $this->_model->scan($this->_testDir, $filePatterns);
        $expected = array(
            'php' => array(
                $this->_testDir . '/additional.php',
                $this->_testDir . '/app/bootstrap.php',
                $this->_testDir . '/app/code/Magento/SomeModule/Helper/Test.php',
                $this->_testDir . '/app/code/Magento/SomeModule/Model/Test.php'
            ),
            'config' => array(
                $this->_testDir . '/app/code/Magento/SomeModule/etc/adminhtml/system.xml',
                $this->_testDir . '/app/code/Magento/SomeModule/etc/config.xml'
            ),
            'view' => array($this->_testDir . '/app/code/Magento/SomeModule/view/frontend/default.xml'),
            'design' => array($this->_testDir . '/app/design/adminhtml/magento_backend/layout.xml'),
            'etc' => array($this->_testDir . '/app/etc/additional.xml', $this->_testDir . '/app/etc/config.xml')
        );
        $this->assertEquals(sort($expected['php']), sort($actual['php']), 'Incorrect php files list');
        $this->assertEquals(sort($expected['config']), sort($actual['config']), 'Incorrect config files list');
        $this->assertEquals(sort($expected['view']), sort($actual['view']), 'Incorrect view files list');
        $this->assertEquals(sort($expected['design']), sort($actual['design']), 'Incorrect design files list');
        $this->assertEquals(sort($expected['etc']), sort($actual['etc']), 'Incorrect etc files list');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Tools\Di\Code\Scanner;

class PhpScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Di\Code\Scanner\PhpScanner
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
        $this->_model = new \Magento\Tools\Di\Code\Scanner\PhpScanner();
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
        $this->_testFiles = array(
            $this->_testDir . '/app/code/Magento/SomeModule/Helper/Test.php',
            $this->_testDir . '/app/code/Magento/SomeModule/Model/Test.php',
            $this->_testDir . '/app/bootstrap.php',
        );
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Magento\SomeModule\ElementFactory',
            'Magento\SomeModule\BlockFactory',
            'Magento\SomeModule\ModelFactory',
            'Magento\SomeModule\Model\BlockFactory',
            'Magento\Bootstrap\ModelFactory',
        );
        $this->assertEquals($expected, $actual);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\System\Configuration;

require_once realpath(__DIR__ . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/LoggerAbstract.php';

class LoggerAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\System\Configuration\LoggerAbstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento\Tools\Migration\System\Configuration\LoggerAbstract');
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers \Magento\Tools\Migration\System\Configuration\LoggerAbstract::add()
     * @covers \Magento\Tools\Migration\System\Configuration\LoggerAbstract::__toString()
     */
    public function testToString()
    {
        $this->_model->add('file1', \Magento\Tools\Migration\System\Configuration\LoggerAbstract::FILE_KEY_VALID);
        $this->_model->add('file2', \Magento\Tools\Migration\System\Configuration\LoggerAbstract::FILE_KEY_INVALID);

        $expected = 'valid: 1' . PHP_EOL
            . 'invalid: 1' . PHP_EOL
            . 'Total: 2' . PHP_EOL
            . '------------------------------' . PHP_EOL
            . 'valid:' . PHP_EOL
            . 'file1' . PHP_EOL
            . '------------------------------' . PHP_EOL
            . 'invalid:' . PHP_EOL
            . 'file2';

        $this->assertEquals($expected, (string)$this->_model);
    }
}


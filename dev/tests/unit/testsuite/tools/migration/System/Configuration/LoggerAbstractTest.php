<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../')
    . '/tools/migration/System/Configuration/LoggerAbstract.php';

class Tools_Migration_System_Configuration_LoggerAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Configuration_LoggerAbstract
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Tools_Migration_System_Configuration_LoggerAbstract');
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Tools_Migration_System_Configuration_LoggerAbstract::add()
     * @covers Tools_Migration_System_Configuration_LoggerAbstract::__toString()
     */
    public function testToString()
    {
        $this->_model->add('file1', Tools_Migration_System_Configuration_LoggerAbstract::FILE_KEY_VALID);
        $this->_model->add('file2', Tools_Migration_System_Configuration_LoggerAbstract::FILE_KEY_INVALID);

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


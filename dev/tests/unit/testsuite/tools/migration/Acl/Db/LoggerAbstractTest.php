<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/migration/Acl/Db/LoggerAbstract.php';

class Tools_Migration_Acl_Db_LoggerAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_Acl_Db_LoggerAbstract
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Tools_Migration_Acl_Db_LoggerAbstract');
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Tools_Migration_Acl_Db_LoggerAbstract::add()
     * @covers Tools_Migration_Acl_Db_LoggerAbstract::__toString()
     */
    public function testToString()
    {
        $this->_model->add('key1', 'key2', 3); // mapped item
        $this->_model->add('key2', null, false); // not mapped item
        $this->_model->add(null, 'Some_Module::acl_resource', false); //item in actual format

        $expected = 'Mapped items count: 1' . PHP_EOL 
            . 'Not mapped items count: 1' . PHP_EOL
            . 'Items in actual format count: 1' . PHP_EOL
            . '------------------------------' . PHP_EOL
            . 'Mapped items:' . PHP_EOL
            . 'key1 => key2 :: Count updated rules: 3' . PHP_EOL
            . '------------------------------' . PHP_EOL
            . 'Not mapped items:' . PHP_EOL
            . 'key2' . PHP_EOL
            . '------------------------------' . PHP_EOL
            . 'Items in actual format:' . PHP_EOL
            . 'Some_Module::acl_resource' . PHP_EOL
            . '------------------------------' . PHP_EOL;

        $this->assertEquals($expected, (string)$this->_model);
    }
}


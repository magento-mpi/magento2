<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Db_MysqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Test_Db_Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_model = $this->getMock(
            'Magento_Test_Db_Mysql',
            array('_createScript'),
            array('host', 'user', 'pass', 'schema', __DIR__, $this->_shell)
        );
    }

    protected function tearDown()
    {
        $this->_shell = null;
        $this->_model = null;
    }

    public function testCleanup()
    {
        $expectedSqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'drop_create_database.sql';
        $this->_model
            ->expects($this->once())
            ->method('_createScript')
            ->with($expectedSqlFile, 'DROP DATABASE `schema`; CREATE DATABASE `schema`')
        ;
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with(
                'mysql --protocol=TCP --host=%s --user=%s --password=%s %s < %s',
                array('host', 'user', 'pass', 'schema', $expectedSqlFile)
            )
        ;
        $this->_model->cleanup();
    }
}

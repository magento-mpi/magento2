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

class Magento_Test_Db_OracleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Test_Db_Oracle|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_model = $this->getMock(
            'Magento_Test_Db_Oracle',
            array('_createScript'),
            array('host', 'user', 'pass', 'schema/sid', __DIR__, $this->_shell)
        );
    }

    protected function tearDown()
    {
        $this->_shell = null;
        $this->_model = null;
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Oracle DB schema must be specified in the following format: "<host>/<SID>".
     */
    public function testConstructorException()
    {
        new Magento_Test_Db_Oracle('host', 'user', 'pass', 'schema', __DIR__, $this->_shell);
    }

    public function testCleanup()
    {
        $expectedSqlFile = realpath(__DIR__ . '/../../../../../../Magento/Test/Db/cleanup_database.oracle.sql');
        $this->_model
            ->expects($this->never())
            ->method('_createScript')
        ;
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with(
                'sqlplus %s/%s@%s/%s @%s',
                array('user', 'pass', 'schema', 'sid', $expectedSqlFile)
            )
        ;
        $this->_model->cleanup();
    }
}


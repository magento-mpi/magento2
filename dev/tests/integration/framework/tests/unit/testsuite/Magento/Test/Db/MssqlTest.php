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

class Magento_Test_Db_MssqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Test_Db_Mssql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_model = $this->getMock(
            'Magento_Test_Db_Mssql',
            array('_createScript'),
            array('host', 'user', 'pass', 'schema', __DIR__, $this->_shell)
        );
    }

    protected function tearDown()
    {
        $this->_shell = null;
        $this->_model = null;
    }

    /**
     * Setup expectation for an command to be invoked
     *
     * @param string $command
     * @param PHPUnit_Framework_MockObject_Matcher_Invocation $invocation
     * @param PHPUnit_Framework_MockObject_Stub $stub
     */
    protected function _expectCommand($command, $invocation, PHPUnit_Framework_MockObject_Stub $stub = null)
    {
        /** @var $invocationMocker PHPUnit_Framework_MockObject_Builder_InvocationMocker */
        $invocationMocker = $this->_shell->expects($invocation);
        $invocationMocker
            ->method('execute')
            ->with($command)
        ;
        if ($stub) {
            $invocationMocker->will($stub);
        }
    }

    /**
     * Setup expectation for a SQL file creation
     *
     * @param string $expectedSqlFile
     */
    protected function _expectSqlFileCreation($expectedSqlFile)
    {
        $expectedSql = "USE [master]
GO
ALTER DATABASE [schema] SET SINGLE_USER WITH ROLLBACK IMMEDIATE
GO
DROP DATABASE [schema]
GO
CREATE DATABASE [schema]
GO
USE [schema]
GO
exit
";
        $this->_model
            ->expects($this->once())
            ->method('_createScript')
            ->with($expectedSqlFile, $expectedSql)
        ;
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Neither command line utility "sqlcmd" nor "tsql" is installed.
     */
    public function testCleanupSqlClientNotInstalled()
    {
        $commandException = new Magento_Exception('command not found');
        $this->_expectCommand('sqlcmd -?', $this->at(0), $this->throwException($commandException));
        $this->_expectCommand('tsql -C', $this->at(1), $this->throwException($commandException));
        $this->_model->cleanup();
    }

    public function testCleanupSqlClientSqlcmd()
    {
        $expectedSqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'mssql_cleanup_database.sql';
        $this->_expectCommand('sqlcmd -?', $this->at(0));
        $this->_expectSqlFileCreation($expectedSqlFile);
        $this->_shell
            ->expects($this->at(1))
            ->method('execute')
            ->with('sqlcmd -S %s -U %s -P %s < %s', array('host', 'user', 'pass', $expectedSqlFile))
        ;
        $this->_model->cleanup();
    }

    public function testCleanupSqlClientTsql()
    {
        $expectedSqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'mssql_cleanup_database.sql';
        $commandException = new Magento_Exception('sqlcmd not found');
        $this->_expectCommand('sqlcmd -?', $this->at(0), $this->throwException($commandException));
        $this->_expectCommand('tsql -C', $this->at(1));
        $this->_expectSqlFileCreation($expectedSqlFile);
        $this->_shell
            ->expects($this->at(2))
            ->method('execute')
            ->with('tsql -S %s -U %s -P %s < %s', array('host', 'user', 'pass', $expectedSqlFile))
        ;
        $this->_model->cleanup();
    }
}


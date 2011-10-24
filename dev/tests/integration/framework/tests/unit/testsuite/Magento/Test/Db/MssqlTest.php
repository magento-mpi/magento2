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
     * @var string
     */
    protected $_varDir;

    /**
     * @var Magento_Test_Db_Mssql
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_commandPrefix;

    protected function setUp()
    {
        $this->_varDir  = $this->_varDir  = sys_get_temp_dir();
        $this->_model = $this->getMock(
            'Magento_Test_Db_Mssql',
            array('_exec', '_createScript', 'getExternalProgram'),
            array('host', 'user', 'pass', 'schema', $this->_varDir)
        );
        $this->_model->expects($this->any())
            ->method('getExternalProgram')
            ->will($this->returnValue('sqlcmd'));
        $this->_commandPrefix = 'sqlcmd -S ' . escapeshellarg('host') . ' -U ' . escapeshellarg('user')
            . ' -P ' . escapeshellarg('pass');
    }

    /**
     * @expectedException Exception
     */
    public function testGetExternalProgram()
    {
        $this->_model = $this->getMock('Magento_Test_Db_Mssql', array('_exec'),
            array('host', 'user', 'pass', 'schema', $this->_varDir)
        );
        $this->_model->expects($this->exactly(2))
            ->method('_exec')
            ->will($this->returnValue(false));
        $this->_model->getExternalProgram();
    }

    public function testVerifyEmptyDatabase()
    {
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with($this->_commandPrefix . ' < ' . escapeshellarg($this->_varDir . '/mssql_backup_script.sql'));
        $this->_model->verifyEmptyDatabase();
    }

    public function testCleanup()
    {
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with($this->_commandPrefix . ' < ' . escapeshellarg($this->_varDir . '/mssql_restore_script.sql'));
        $this->_model->cleanup();
    }

    public function testCreateBackup()
    {
        $query = "BACKUP DATABASE [schema] TO DISK=N'schema_test.bak' WITH NOFORMAT, NOINIT, SKIP, NOREWIND, NOUNLOAD"
            . "\nGO\nexit\n";

        $this->_model->expects($this->once())
            ->method('_createScript')
            ->with($this->_varDir . '/mssql_backup_script.sql', $query);
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with($this->_commandPrefix . ' < ' . escapeshellarg($this->_varDir . '/mssql_backup_script.sql'));
        $this->_model->createBackup('test');
    }

    public function testRestoreBackup()
    {
        $this->_model->expects($this->once())
            ->method('_createScript')
            ->with(
                $this->_varDir . '/mssql_restore_script.sql',
                'USE [master]
GO
ALTER DATABASE [schema] SET SINGLE_USER WITH ROLLBACK IMMEDIATE
GO
RESTORE DATABASE [schema] FROM DISK=N\'schema_test.bak\' WITH REPLACE
GO
ALTER DATABASE [schema] SET MULTI_USER WITH NO_WAIT
GO
exit
');
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with($this->_commandPrefix . ' < ' . escapeshellarg($this->_varDir . '/mssql_restore_script.sql'));
        $this->_model->restoreBackup('test');
    }
}


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
     * @var string
     */
    protected $_varDir;

    /**
     * @var Magento_Test_Db_Oracle
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_accessParams;

    protected function setUp()
    {
        $this->_varDir  = sys_get_temp_dir();
        $this->_model = $this->getMock(
            'Magento_Test_Db_Oracle',
            array('_exec', '_createScript'),
            array('host', 'user', 'pass', 'schema/sid', $this->_varDir)
        );
        $this->_accessParams = escapeshellarg('user') . '/' . escapeshellarg('pass')
            . '@' . escapeshellarg('schema') . '/' . escapeshellarg('sid');
    }

    /**
     * @expectedException Exception
     */
    public function test__construct()
    {
        new Magento_Test_Db_Oracle('host', 'user', 'pass', 'schema', $this->_varDir);
    }

    public function testCleanup()
    {
        $dir = realpath(__DIR__ . '/../../../../../../Magento/Test/Db');
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with('sqlplus ' . $this->_accessParams . ' @' . escapeshellarg($dir . '/cleanup_database.oracle.sql'));
        $this->_model->cleanup();
    }

    public function testCreateBackup ()
    {
        $cmd = 'expdp ' . $this->_accessParams . ' SCHEMAS=' . escapeshellarg('user')
            . ' DIRECTORY=' . escapeshellarg('user_bak') . ' DUMPFILE=' . escapeshellarg('test.dmp')
            . ' NOLOGFILE=Y REUSE_DUMPFILES=Y';
        $this->_model->expects($this->once())
            ->method('_exec')
            ->with($cmd);
        $this->_model->createBackup('test');
    }

    public function testRestoreBackup ()
    {
        $dir = realpath(__DIR__ . '/../../../../../../Magento/Test/Db');
        $this->_model->expects($this->at(0))
            ->method('_exec')
            ->with('sqlplus ' . $this->_accessParams . ' @' . escapeshellarg($dir . '/cleanup_database.oracle.sql'));
        $command = 'impdp ' . $this->_accessParams . ' DIRECTORY=' . escapeshellarg('user_bak')
            . ' DUMPFILE=' . escapeshellarg('test.dmp') . ' SCHEMAS=' . escapeshellarg('user');
        $this->_model->expects($this->at(1))
            ->method('_exec')
            ->with($command);
        $this->_model->restoreBackup('test');
    }
}


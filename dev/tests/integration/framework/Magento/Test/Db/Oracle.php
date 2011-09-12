<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Oracle platform database handler
 */
class Magento_Test_Db_Oracle extends Magento_Test_Db_DbAbstract
{
    /**
     * @var string
     */
    protected $_sid = '';

    /**
     * Oracle has different notation of the schema and host
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param string $dumpFile
     * @throws Exception on invalid "schema" format
     */
    public function __construct($host, $user, $password, $schema, $varPath)
    {
        if (!preg_match('/^[^\/]+\/[^\/]+$/', $schema)) {
            throw new Exception('Oracle DB schema must be specified in the following format: "<host>/<SID>".');
        }
        // the original host is ignored, but instead taken from schema notation
        list($host, $sid) = explode('/', $schema);
        $this->_sid = $sid;

        // user and DB name are equivalent in Oracle
        $schema = $user;

        parent::__construct($host, $user, $password, $schema, $varPath);
    }

    /**
     * Remove all DB objects
     *
     * @return bool
     */
    public function cleanup()
    {
        $cmd = sprintf('sqlplus %s/%s@%s/%s @%s',
            escapeshellarg($this->_user),
            escapeshellarg($this->_password),
            escapeshellarg($this->_host),
            escapeshellarg($this->_sid),
            escapeshellarg(__DIR__ . '/cleanup_database.oracle.sql')
        );
        return $this->_exec($cmd);
    }

    /**
     * Create database backup
     *
     * @param string $name
     * @return bool
     */
    public function createBackup($name)
    {
        $cmd = sprintf('expdp %s/%s@%s/%s SCHEMAS=%s DIRECTORY=%s DUMPFILE=%s NOLOGFILE=Y REUSE_DUMPFILES=Y',
            escapeshellarg($this->_user),
            escapeshellarg($this->_password),
            escapeshellarg($this->_host),
            escapeshellarg($this->_sid),
            escapeshellarg($this->_schema),
            escapeshellarg($this->_getBakDirName()),
            escapeshellarg($this->_getBackupFile($name))
        );
        return $this->_exec($cmd);
    }

    /**
     * Restore database from backup
     *
     * @param string $name
     * @return bool
     */
    public function restoreBackup($name)
    {
        /**
         * Remove all user defined objects because backup doesn't contain drop directives
         */
        $this->cleanup();
        $cmd = sprintf('impdp %s/%s@%s/%s DIRECTORY=%s DUMPFILE=%s SCHEMAS=%s',
            escapeshellarg($this->_user),
            escapeshellarg($this->_password),
            escapeshellarg($this->_host),
            escapeshellarg($this->_sid),
            escapeshellarg($this->_getBakDirName()),
            escapeshellarg($this->_getBackupFile($name)),
            escapeshellarg($this->_schema)
        );
        return $this->_exec($cmd);
    }

    /**
     * Get backup file name based on backup name
     *
     * @param  $name
     * @return string
     */
    protected function _getBackupFile($name)
    {
        return $name . '.dmp';
    }

    /**
     * Generate backup dir name on Oracle server
     *
     * @return string
     */
    protected function _getBakDirName()
    {
        return "{$this->_schema}_bak";
    }
}

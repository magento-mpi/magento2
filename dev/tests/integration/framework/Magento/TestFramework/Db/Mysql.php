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
 * MySQL platform database handler
 */
namespace Magento\TestFramework\Db;

class Mysql extends \Magento\TestFramework\Db\AbstractDb
{
    /**
     * Remove all DB objects
     */
    public function cleanup()
    {
        $this->_shell->execute(
            'mysql --host=%s --user=%s --password=%s %s -e %s',
            array(
                $this->_host,
                $this->_user,
                $this->_password,
                $this->_schema,
                "DROP DATABASE `{$this->_schema}`; CREATE DATABASE `{$this->_schema}`"
            )
        );
    }

    /**
     * Get filename for setup db dump
     *
     * @return string
     */
    protected function getSetupDbDumpFilename()
    {
        return $this->_varPath . '/setup_dump.sql';
    }

    /**
     * Is dump esxists
     *
     * @return bool
     */
    public function isDbDumpExists()
    {
        return file_exists($this->getSetupDbDumpFilename());
    }

    /**
     * Store setup db dump
     */
    public function storeDbDump()
    {
        if ($this->_password) {
            $this->_shell->execute(
                'mysqldump --host=%s --user=%s --password=%s %s > %s',
                array($this->_host, $this->_user, $this->_password, $this->_schema, $this->getSetupDbDumpFilename())
            );
        } else {
            $this->_shell->execute(
                'mysqldump --host=%s --user=%s %s > %s',
                array($this->_host, $this->_user, $this->_schema, $this->getSetupDbDumpFilename())
            );
        }
    }

    /**
     * Restore db from setup db dump
     */
    public function restoreFromDbDump()
    {
        $this->_shell->execute(
            'mysql --host=%s --user=%s --password=%s %s < %s',
            array($this->_host, $this->_user, $this->_password, $this->_schema, $this->getSetupDbDumpFilename())
        );
    }
}

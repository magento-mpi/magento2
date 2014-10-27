<?php
/**
 * {license_notice}
 *
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
     * Defaults extra file name
     */
    const DEFAULTS_EXTRA_FILE_NAME = 'defaults_extra.cnf';

    /**
     * Set initial essential parameters
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param string $varPath
     * @param \Magento\Framework\Shell $shell
     * @throws \Magento\Framework\Exception
     */
    public function __construct($host, $user, $password, $schema, $varPath, \Magento\Framework\Shell $shell)
    {
        parent::__construct($host, $user, $password, $schema, $varPath, $shell);
        $this->_createDefaultsExtra();
    }

    /**
     * Remove all DB objects
     */
    public function cleanup()
    {
        $this->_shell->execute(
            'mysql --defaults-extra-file=%s --host=%s %s -e %s',
            array(
                $this->_getDefaultsExtraFileName(),
                $this->_host,
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
        return $this->_varPath . '/setup_dump_' . $this->_schema . '.sql';
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
        $this->_shell->execute(
            'mysqldump --defaults-extra-file=%s --host=%s  %s > %s',
            array($this->_getDefaultsExtraFileName(), $this->_host, $this->_schema, $this->getSetupDbDumpFilename())
        );
    }

    /**
     * Restore db from setup db dump
     */
    public function restoreFromDbDump()
    {
        $this->_shell->execute(
            'mysql --defaults-extra-file=%s --host=%s %s < %s',
            array($this->_getDefaultsExtraFileName(), $this->_host, $this->_schema, $this->getSetupDbDumpFilename())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVendorName()
    {
        return 'mysql';
    }

    /**
     * Get defaults extra file name
     *
     * @return string
     */
    protected function _getDefaultsExtraFileName()
    {
        return rtrim($this->_varPath, '\\/') . '/' . self::DEFAULTS_EXTRA_FILE_NAME;
    }

    /**
     * Create defaults extra file
     */
    protected function _createDefaultsExtra()
    {
        $extraConfig = array('[client]', 'user=' . $this->_user, 'password="' . $this->_password . '"');
        file_put_contents($this->_getDefaultsExtraFileName(), implode(PHP_EOL, $extraConfig));
        chmod($this->_getDefaultsExtraFileName(), 0644);
    }
}

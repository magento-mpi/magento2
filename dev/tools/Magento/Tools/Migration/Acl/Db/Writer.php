<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_Acl_Db_Writer
{
    /**
     * DB adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * Source table name
     *
     * @var string
     */
    protected $_tableName;

    /**
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param string $tableName source table
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter, $tableName)
    {
        $this->_tableName = $tableName;
        $this->_adapter = $adapter;
    }

    /**
     * Update records in database
     *
     * @param $oldKey
     * @param $newKey
     */
    public function update($oldKey, $newKey)
    {
        $this->_adapter->update($this->_tableName,
            array('resource_id' => $newKey),
            array('resource_id = ?' => $oldKey)
        );
    }
}

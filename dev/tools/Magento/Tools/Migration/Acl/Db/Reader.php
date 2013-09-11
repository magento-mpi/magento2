<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Db adapter. Reader.
 * Get unique acl resource identifiers from source table
 */
class Magento_Tools_Migration_Acl_Db_Reader
{
    /**
     * Source table name
     *
     * @var string
     */
    protected $_tableName;

    /**
     * DB adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

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
     * Get list of unique resource identifiers
     * Format: [resource] => [count items]
     * @return array
     */
    public function fetchAll()
    {
        $select = $this->_adapter->select();
        $select->from($this->_tableName, array())
            ->columns(array('resource_id' => 'resource_id', 'itemsCount' => new Zend_Db_Expr('count(*)')))
            ->group('resource_id');
        return $this->_adapter->fetchPairs($select);
    }
}



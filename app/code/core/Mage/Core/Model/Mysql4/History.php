<?php
/**
 * Data change history model
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_History
{
    protected $_changeTable = null;
    protected $_changeInfoTable = null;
    
    public function __construct() 
    {
        $this->_changeTable = Mage::getSingleton('core/resource')->getTableName('core/data_change');
        $this->_changeInfoTable = Mage::getSingleton('core/resource')->getTableName('core/data_change_info');
    }
    
    /**
     * Add data changes
     * 
     * $data = array(
     *      [$tableName] => array(
     *          [pk_value]
     *          [type] = 'insert' || 'update' || 'delete'
     *          [before]
     *          [after]
     *      )
     * )
     * 
     * @param string $code
     * @param int $userId
     * @param array $data
     */
    public function addChanges($code, $userId, $data)
    {
        
    }
}
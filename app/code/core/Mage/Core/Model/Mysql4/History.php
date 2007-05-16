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
    protected static $_changeTable = null;
    protected static $_changeInfoTable = null;
    
    public function __construct() 
    {
        self::$_changeTable = Mage::registry('resources')->getTableName('core_read', 'data_change');
        self::$_changeInfoTable = Mage::registry('resources')->getTableName('core_read', 'data_change_info');
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
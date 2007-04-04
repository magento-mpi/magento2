<?php
/**
 * Db table interface
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
interface Mage_Core_Resource_Model_Db_Table_Interface
{
    /**
     * Insert row in database table
     *
     * @param array $data
     */
    public function insert($data);
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     */
    public function update($data, $rowId);
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId);
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId);
}
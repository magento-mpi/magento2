<?php
/**
 * Product attributes groups
 * 
 * TODO: group and attributes position
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group
{
    protected $_groupTable;
    protected $_inSetTable;
    protected $_read;
    protected $_write;

    public function __construct() 
    {
        $this->_read = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
        $this->_groupTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_group');
        $this->_inSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
    }
    
    /**
     * Get group data
     *
     * @param   int $groupId
     * @param   string || array $fields
     * @return  array
     */
    public function load($groupId)
    {
        $sql = "SELECT * FROM $this->_groupTable WHERE group_id=:group_id";
        $arr = $this->_read->fetchRow($sql, array('group_id'=>$groupId));
        return $arr;
    }

    /**
     * Get group attributes
     *
     * @param   int $groupId
     * @param   int $setId
     * @return  array
     */
    public function getAttributes($groupId, $setId)
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addGroupFilter($groupId)
            ->addSetFilter($setId);
        return $collection;
    }
    
    public function save(Mage_Catalog_Model_Product_Attribute_Group $group)
    {
        $this->_write->beginTransaction();
        try {
            $data = array(
                'code'  => $group->getCode(),
                'set_id'=> $group->getSetId()
            );
            
            if ($group->getId()) {
                $condition = $this->_write->quoteInto('group_id=?', $group->getId());
                $this->_write->update($this->_groupTable, $data, $condition);
            }
            else {
                $this->_write->insert($this->_groupTable, $data);
                $group->setGroupId($this->_write->lastInsertId());
            }
            
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function moveAfter(Mage_Catalog_Model_Product_Attribute_Group $group, $prevGroup)
    {
        
    }
    
    public function delete($groupId)
    {
        $condition = $this->_write->quoteInto('group_id=?', $groupId);
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_groupTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function addAttribute($group, $attribute)
    {
        $this->_write->beginTransaction();
        try {
            $data = array(
                'attribute_id'  => $attribute->getId(),
                'set_id'        => $group->getSetId(),
                'group_id'      => $group->getId()
            );
            $this->_write->insert($this->_inSetTable, $data);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function removeAttribute($group, $attribute)
    {
        $condition = $this->_write->quoteInto('group_id=?', $group->getId()) .
                    ' AND ' . $this->_write->quoteInto('attribute_id=?', $attribute->getId());
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_inSetTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
}
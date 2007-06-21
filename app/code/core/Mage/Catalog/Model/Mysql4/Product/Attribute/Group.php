<?php
/**
 * Product attributes groups
 * 
 * TODO: group and attributes position
 *
 * @package    Mage
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
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_group');
        $this->_inSetTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_in_set');
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
    public function getAttributes($groupId, $onlyVisible=false)
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addGroupFilter($groupId)
            ->setOrder($this->_inSetTable.'.position', 'asc');
        if ($onlyVisible) {
            $collection->addFilter('is_visible', 1);
        }
        $collection->load();
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
        $siblingGroup = false;
        $generalGroupId = $this->getGeneralSiblingId($groupId);
        if ($generalGroupId) {
            $siblingGroup = Mage::getModel('catalog/product_attribute_group')->load($generalGroupId);
            $attributes = $this->getAttributes($groupId);
        }

        
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
        
        if ($siblingGroup) {
            foreach ($attributes as $attribute) {
                $siblingGroup->addAttribute($attribute);
            }
        }
    }
    
    public function getGeneralSiblingId($groupId)
    {
        $sql = "SELECT 
                    group_id
                FROM
                    $this->_groupTable
                WHERE
                    set_id IN (SELECT set_id FROM $this->_groupTable WHERE group_id=:group_id)
                    AND group_id<>:group_id_not
                ORDER BY
                    position";
        return $this->_write->fetchOne($sql, array('group_id'=>$groupId, 'group_id_not'=>$groupId));
    }
    
    public function addAttribute($group, $attribute, $position=null)
    {
        $this->_write->beginTransaction();
        try {
            $groupId = (int)$group->getId();
            if (is_null($position)) {
                $position = (int)$this->_write->fetchOne("select max(position) from $this->_inSetTable where group_id=$groupId");
                $position++;
            } else {
                $position = (int)$position;
                $this->_write->query("update $this->_inSetTable set position=position+1 where group_id=$groupId and position>=$position");
            }
            
            $data = array(
                'attribute_id'  => $attribute->getId(),
                'set_id'        => $group->getSetId(),
                'group_id'      => $groupId,
                'position'      => $position,
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
        $this->_write->beginTransaction();
        try {
            $groupId = (int)$group->getId();
            
            $position = (int)$this->getAttributePosition($group, $attribute);
            
            $condition = $this->_write->quoteInto('group_id=?', $groupId) .
                    ' AND ' . $this->_write->quoteInto('attribute_id=?', $attribute->getId());
            $this->_write->delete($this->_inSetTable, $condition);
            
            if (!empty($position)) {
                $this->_write->query("update $this->_inSetTable set position=position-1 where group_id=$groupId and position>=$position");
            }
            
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function getAttributePosition($group, $attribute)
    {
        $groupId = (int)$group->getId();
        $attributeId = (int)$attribute->getId();
        $position = $this->_read->fetchOne("select position from $this->_inSetTable where group_id=$groupId and attribute_id=$attributeId");
        return $position;
    }
}
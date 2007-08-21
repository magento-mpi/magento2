<?php
/**
 * Report Products Tags collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Tag_Product_Collection extends Mage_Tag_Model_Mysql4_Product_Collection
{
    protected function _construct()
    {
        $this->_init('tag/tag');
    }
    
    public function addUniqueTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(DISTINCT(tr.tag_id))'))
            ->order('taged desc');
        return $this;
    }
    
    public function addAllTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(tr.tag_id)'))
            ->order('taged desc');
        return $this;
    }
    
    public function addTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(tr.tag_relation_id)'))
            ->order('taged desc');
        return $this;
    }
    
    public function addGroupByProduct()
    {
        $this->getSelect()
            ->group('tr.product_id');
        return $this;
    }
    
    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('tr.tag_id');
        return $this;
    }
    
    public function addProductFilter($customerId)
    {
        $this->getSelect()
            ->where('tr.product_id = ?', $customerId);
        $this->_customerFilterId = $customerId;
        return $this;
    }
       
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(t.tag_id) from ', $sql);

        return $sql;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
        if ($attribute == 'taged' || $attribute == 'tag_name') {
                $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
                parent::setOrder($attribute, $dir);    
        }
        
        return $this;
    }
    
}
?>
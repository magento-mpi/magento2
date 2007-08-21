<?php
/**
 * Report Products Tags collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Tag_Collection extends Mage_Tag_Model_Mysql4_Tag_Collection
{
    protected function _construct()
    {
        $this->_init('tag/tag');
    }
       
    public function addGroupByTag()
    {
        $this->getSelect()
            ->joinRight(array('tr' => $this->getTable('tag/relation')), 'main_table.tag_id=tr.tag_id', array('taged' => 'count(tr.tag_relation_id)'))
            ->order('taged desc')
            ->group('main_table.tag_id');
        return $this;
    }
       
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(distinct(name)) from ', $sql);
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
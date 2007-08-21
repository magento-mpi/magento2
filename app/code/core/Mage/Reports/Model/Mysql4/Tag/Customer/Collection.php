<?php
/**
 * Report Customers Tags collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Tag_Customer_Collection extends Mage_Tag_Model_Mysql4_Customer_Collection
{

    public function addTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(tr.tag_relation_id)'))
            ->order('taged desc');
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
        switch( $attribute ) {
            case 'taged':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
                
            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }
    
}
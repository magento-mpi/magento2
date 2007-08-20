<?php
/**
 * Report Customers Review collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Review_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected function _construct()
    {
        parent::__construct();
    }
    
    protected function _joinFields()
    {
        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname');
   
        $this->getSelect()
            ->join('review_detail', 'review_detail.customer_id = e.entity_id', array('review_cnt' => 'count(review_detail.review_id)'))
            ->group('e.entity_id');
        
    }
    
    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
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
        
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(DISTINCT(entity_id)) from ', $sql);
        
        return $sql;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
          
        if ($attribute == 'review_cnt') {
                $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
                parent::setOrder($attribute, $dir);    
        }
        
        return $this;
    }
    
}
?>
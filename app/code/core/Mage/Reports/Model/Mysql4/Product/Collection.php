<?php
/**
 * Products Report collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
    protected function _construct()
    {
        parent::__construct();
    }
    
    protected function _joinFields()
    {
        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name');
        $this->getSelect()->from('', array(
                    'viewed' => 'CONCAT("","")', 
                    'added' => 'CONCAT("","")',
                    'purchased' => 'CONCAT("","")',
                    'fulfilled' => 'CONCAT("","")',
                    'revenue' => 'CONCAT("","")',
                    ));
    }
    
    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
        switch ($attribute)
        {
            case 'viewed':
            case 'added':
            case 'purchased':
            case 'fulfilled':
            case 'revenue':
                $this->getSelect()->order($attribute . ' ' . $dir);    
                break;
            default:
                parent::setOrder($attribute, $dir);    
        }
        
        return $this;
    }
    
}
?>

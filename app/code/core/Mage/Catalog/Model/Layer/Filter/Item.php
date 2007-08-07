<?php
/**
 * Filter item model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_Filter_Item extends Varien_Object
{
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            Mage::throwException('Filter must be as object. Set correct filter please');
        }
        return $filter;
    }
    
    public function getUrl()
    {
        return Mage::getUrl('*/*/*', array('_current'=>true, $this->getFilter()->getRequestVar()=>$this->getValue()));
    }
    
    public function getRemoveUrl()
    {
        return Mage::getUrl('*/*/*', array('_current'=>true, $this->getFilter()->getRequestVar()=>null));
    }
    
    public function getName()
    {
        return $this->getFilter()->getName();
    }
}

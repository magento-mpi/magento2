<?php
/**
 * Customer group attribute source
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity_Customer_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
            foreach ($this->_options as $i=>$o) {
            	if ($o['value']==0) {
            		unset($this->_options[$i]);
            	}
            }
        }
        return $this->_options;
    }
}

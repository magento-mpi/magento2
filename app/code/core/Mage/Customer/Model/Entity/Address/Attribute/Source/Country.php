<?php
/**
 * Customer country attribute source
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity_Address_Attribute_Source_Country extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();
            $baseUrl = Mage::getBaseUrl();
            foreach ($this->_options as $index=>$option) {
            	$this->_options[$index]['style'] = 'background-image:url('.$baseUrl.'skins/default/images/icons/flags/'.strtolower($option['title']).'.gif)';
            }
        }
        return $this->_options;
    }
}

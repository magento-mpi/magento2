<?php
/**
 * Product visibility attribute source model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product_Attribute_Source_Visibility extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('catalog/product_visibility_collection')
                ->load()
                ->toOptionArray();
                
            array_unshift($this->_options, array('label'=>'', 'value'=>''));
        }
        return $this->_options;
    }
}

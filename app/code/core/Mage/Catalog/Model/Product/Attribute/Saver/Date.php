<?php
/**
 * Product date attribute saver
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Saver_Date extends Mage_Catalog_Model_Product_Attribute_Saver 
{
    public function save($productId, $value)
    {
        if (empty($value) && $this->_attribute->isRequired()) {
            $value = now();
        }
        else {
            return $this;
        }
        return parent::save($productId, $value);
    }
}
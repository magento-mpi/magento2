<?php
/**
 * Product data validation class
 * 
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Validate_Product extends Mage_Core_Validate 
{
    /**
     * Data validation
     */
    public function isValid() 
    {
        $this->_data = $this->_prepareArray($this->_data, array('setid', 'attributes'));
        $validateSetId = $this->_getValidator('int');

        if (!$validateSetId->isValid($this->_data['setid'])) {
            $this->_message = 'Empty attribute set ID';
            return false;
        }
    }

}

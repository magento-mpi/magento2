<?php
/**
 * Websites collection
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Website_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct() 
    {
        $this->_init('core/website');
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('website_id', 'name');
    }
}
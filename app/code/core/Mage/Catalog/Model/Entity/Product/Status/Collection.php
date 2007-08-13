<?php
/**
 * Product status collection resource model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Status_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct()
    {
        $this->_init('catalog/product_status');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('status_id', 'status_code');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('status_id', 'status_code');
    }

}

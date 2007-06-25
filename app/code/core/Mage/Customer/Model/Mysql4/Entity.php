<?php
/**
 * Customer entity resource model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Mysql4_Entity extends Mage_Core_Model_Mysql4_Entity 
{
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_read        = $resource->getConnection('customer_read');
        $this->_write       = $resource->getConnection('customer_write');
    }
}
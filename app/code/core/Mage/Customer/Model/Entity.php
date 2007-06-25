<?php
/**
 * Customer entity
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity extends Mage_Core_Model_Entity
{
    public function __construct() 
    {
        parent::__construct('customer');
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('customer/entity');
    }
}

<?php
/**
 * Customer address entity resource model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Customer_Model_Entity_Address extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }
    
    public function getCustomerId($object)
    {
        return $object->getData('customer_id') ? $object->getData('customer_id') :$object->getParentId();
    }
    
    public function setCustomerId($object, $id)
    {
        $object->setParentId($id);
        $object->setData('customer_id', $id);
        return $object;
    }
}
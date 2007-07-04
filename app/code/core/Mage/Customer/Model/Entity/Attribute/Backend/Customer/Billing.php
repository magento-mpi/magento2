<?php
/**
 * Customer default billing address backend
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity_Attribute_Backend_Customer_Billing extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        if ($defaultBilling = $object->getDefaultBilling()) 
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddressCollection() as $address) {
            	if ($address->getPostIndex() == $defaultBilling) {
            	    $addressId = $address->getId();
            	}
            }
            
            if ($addressId) {
                $object->setDefaultBilling($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getName());
            }
        }
    }
}

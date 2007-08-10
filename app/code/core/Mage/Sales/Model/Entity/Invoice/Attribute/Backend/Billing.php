<?php
/**
 * Invoice billing address backend
 *
 * @package     Mage
 * @subpackage  Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Model_Entity_Invoice_Attribute_Backend_Billing extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    public function beforeSave($object)
    {
        $billingAddressId = $object->getBillingAddressId();
        if (is_null($billingAddressId)) {
            $object->unsetBillingAddressId();
        }
    }

    public function afterSave($object)
    {
        $billingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
        	if ('billing' == $address->getAddressType()) {
        	    $billingAddressId = $address->getId();
        	}
        }
        if ($billingAddressId) {
            $object->setBillingAddressId($billingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }
    }

}

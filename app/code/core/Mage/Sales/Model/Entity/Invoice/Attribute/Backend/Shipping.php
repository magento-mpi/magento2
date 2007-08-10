<?php
/**
 * Invoice shipping address backend
 *
 * @package     Mage
 * @subpackage  Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Model_Entity_Invoice_Attribute_Backend_Shipping extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    public function beforeSave($object)
    {
        $shippingAddressId = $object->getShippingAddressId();
        if (is_null($shippingAddressId)) {
            $object->unsetShippingAddressId();
        }
    }

    public function afterSave($object)
    {
        $shippingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
        	if ('shipping' == $address->getAddressType()) {
        	    $shippingAddressId = $address->getId();
        	}
        }
        if ($shippingAddressId) {
            $object->setShippingAddressId($shippingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }
    }

}

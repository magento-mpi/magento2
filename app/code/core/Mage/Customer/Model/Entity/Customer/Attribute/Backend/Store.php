<?php
/**
 * Store attribute backend
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Customer_Model_Entity_Customer_Attribute_Backend_Store extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }
        if (! $object->hasData('created_in')) {
            $object->setData('created_in', $object->getData('store_id'));
        }
        return $this;
    }
}

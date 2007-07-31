<?php
/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Time_Created extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        if (!$object->getId()) {
            $object->setData($this->getAttribute()->getAttributeCode(), now());
        }
    }
}
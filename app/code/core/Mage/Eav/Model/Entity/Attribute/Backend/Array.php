<?php
/**
 * Backend model for attribute with multiple values
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Array extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $data = $object->getData($this->getAttribute()->getAttributeCode());
        if (is_array($data)) {
            $object->setData($this->getAttribute()->getAttributeCode(), implode(',', $data));
        }
        return parent::beforeSave($object);
    }
}

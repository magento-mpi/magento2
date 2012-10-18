<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for serialized array data
 *
 */
class Mage_Backend_Model_Config_Backend_Serialized_Array extends Mage_Backend_Model_Config_Backend_Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        parent::_beforeSave();
    }
}

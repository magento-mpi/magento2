<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Visual design editor manager adapter
 */
class Mage_DesignEditor_Model_History_Manager_Adapter extends Mage_Core_Model_Abstract
{
    /**
     * Layout change type
     */
    const CHANGE_TYPE_LAYOUT = 'layout';

    /**
     * Get change by type
     *
     * @static
     * @throws Mage_DesignEditor_Exception
     * @param string $adapter
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Abstract
     */
    public static function factory($adapter)
    {
        switch ($adapter) {
            case self::CHANGE_TYPE_LAYOUT:
                return Mage::getModel('Mage_DesignEditor_Model_History_Manager_Adapter_Layout');
                break;
            default:
                throw new Mage_DesignEditor_Exception(
                    Mage::helper('Mage_DesignEditor_Helper_Data')->__('Change type %s is not exist.', $adapter)
                );
                break;
        }
    }
}

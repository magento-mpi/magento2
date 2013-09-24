<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
class Magento_Ogone_Model_Source_Pmlist implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Prepare ogone payment block layout as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Ogone_Model_Api::PMLIST_HORISONTAL_LEFT, 'label' => __('Horizontally grouped logo with group name on left')),
            array('value' => Magento_Ogone_Model_Api::PMLIST_HORISONTAL, 'label' => __('Horizontally grouped logo with no group name')),
            array('value' => Magento_Ogone_Model_Api::PMLIST_VERTICAL, 'label' => __('Verical list')),
        );
    }
}

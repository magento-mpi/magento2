<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
class Mage_Ogone_Model_Source_Pmlist
{
    /**
     * Prepare ogone payment block layout as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Ogone_Model_Api::PMLIST_HORISONTAL_LEFT, 'label' => __('Horizontally grouped logo with group name on left')),
            array('value' => Mage_Ogone_Model_Api::PMLIST_HORISONTAL, 'label' => __('Horizontally grouped logo with no group name')),
            array('value' => Mage_Ogone_Model_Api::PMLIST_VERTICAL, 'label' => __('Verical list')),
        );
    }
}

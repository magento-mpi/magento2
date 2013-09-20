<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for admin password change mode
 *
 */
class Magento_Pci_Model_System_Config_Source_Password extends Magento_Object
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => __('Recommended'),
            ),
            array(
                'value' => 1,
                'label' => __('Forced'),
            ),
        );
    }
}

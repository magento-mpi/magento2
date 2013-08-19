<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
 */
class Magento_Backend_Model_Config_Source_Yesnocustom implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>__('Yes')),
            array('value' => 0, 'label'=>__('No')),
            array('value' => 2, 'label'=>__('Specified'))
        );
    }

}

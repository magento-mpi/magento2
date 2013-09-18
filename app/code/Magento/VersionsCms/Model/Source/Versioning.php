<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Versioning configuration source model
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Versioning implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Enabled by Default'),
            '1' => __('Disabled by Default')
        );
    }
}

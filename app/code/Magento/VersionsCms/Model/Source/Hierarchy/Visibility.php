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
 * Visibility option source model for Hierarchy metadata
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Visibility implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_VersionsCms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT => __('Use Parent'),
            Magento_VersionsCms_Helper_Hierarchy::METADATA_VISIBILITY_YES => __('Yes'),
            Magento_VersionsCms_Helper_Hierarchy::METADATA_VISIBILITY_NO => __('No'),
        );
    }
}

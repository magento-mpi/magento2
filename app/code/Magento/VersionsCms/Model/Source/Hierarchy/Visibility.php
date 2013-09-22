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

class Visibility implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\VersionsCms\Helper\Hierarchy::METADATA_VISIBILITY_PARENT => __('Use Parent'),
            \Magento\VersionsCms\Helper\Hierarchy::METADATA_VISIBILITY_YES => __('Yes'),
            \Magento\VersionsCms\Helper\Hierarchy::METADATA_VISIBILITY_NO => __('No'),
        );
    }
}

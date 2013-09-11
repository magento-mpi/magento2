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
namespace Magento\VersionsCms\Model\Source\Hierarchy;

class Visibility
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

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Visibility option source model for Hierarchy metadata
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Visibility
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT => __('Use Parent'),
            Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES => __('Yes'),
            Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_NO => __('No'),
        );
    }
}

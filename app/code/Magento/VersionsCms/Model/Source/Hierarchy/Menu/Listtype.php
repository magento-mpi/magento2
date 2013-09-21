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
 * CMS Hierarchy Navigation Menu source model for list type
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Menu_Listtype implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0'  => __('Unordered'),
            '1' => __('Ordered'),
        );
    }
}

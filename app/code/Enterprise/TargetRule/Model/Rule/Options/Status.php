<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Rule_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1 => __('Active'),
            0 => __('Inactive'),
        );
    }
}

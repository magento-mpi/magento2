<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterpise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment statuses option array
 *
 * @category   Enterprise
 * @package    Enterpise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Resource_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1 => "Active",
            0 => "Inactive",
        );
    }
}

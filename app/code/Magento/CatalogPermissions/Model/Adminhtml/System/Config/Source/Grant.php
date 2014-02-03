<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for grant permission select
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source;

use Magento\CatalogPermissions\Helper\Data;
use Magento\Core\Model\Option\ArrayInterface;

class Grant implements ArrayInterface
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Data::GRANT_ALL => __('Yes, for Everyone'),
            Data::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            Data::GRANT_NONE => __('No')
        );
    }
}

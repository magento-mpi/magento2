<?php
/**
 * Configuration source for grant permission select
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant;

use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\Option\ArrayInterface;

class Landing implements ArrayInterface
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            ConfigInterface::GRANT_ALL => __('Yes, for Everyone'),
            ConfigInterface::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            ConfigInterface::GRANT_NONE => __('No, Redirect to Landing Page')
        );
    }
}

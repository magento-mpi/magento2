<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration\Source;

/**
 * Integration authentication options.
 */
class Authentication implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve authentication options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Integration\Model\Integration::AUTHENTICATION_OAUTH => __('OAuth'),
            \Magento\Integration\Model\Integration::AUTHENTICATION_MANUAL => __('Manual'),
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module\Setup;

/**
 * Simplified resource config for Setup tools
 */
class ResourceConfig implements \Magento\Framework\App\Resource\ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConnectionName($resourceName)
    {
        return \Magento\Framework\App\Resource\Config::DEFAULT_SETUP_CONNECTION;
    }
}

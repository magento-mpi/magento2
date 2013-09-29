<?php
/**
 * Application config storage interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

interface StorageInterface
{
    /**
     * Get loaded configuration
     *
     * @return \Magento\Core\Model\ConfigInterface
     */
    public function getConfiguration();

    /**
     * Remove configuration cache
     */
    public function removeCache();
}

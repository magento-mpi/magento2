<?php
/**
 * Application config loader interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

interface LoaderInterface
{
    /**
     * Populate configuration object
     *
     * @param \Magento\Core\Model\Config\Base $config
     */
    public function load(\Magento\Core\Model\Config\Base $config);
}

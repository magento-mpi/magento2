<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Data;

/**
 * Config
 */
interface ConfigInterface
{
    /**
     * Get modules
     *
     * @return array
     */
    public function getModules();

    /**
     * Get total dependencies count
     *
     * @return int
     */
    public function getDependenciesCount();
}

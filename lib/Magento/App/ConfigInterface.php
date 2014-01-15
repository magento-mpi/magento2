<?php
/**
 * Configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

interface ConfigInterface
{
    /**
     * Retrieve config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path = null);

    /**
     * Set config value
     *
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value);

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @return bool
     */
    public function getFlag($path);
}

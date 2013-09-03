<?php
/**
 * Store Config Interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Core_Model_Store_ConfigInterface
{
    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public function getConfig($path, $store = null);

    /**
     * Retrieve store config flag
     *
     * @param string $path
     * @param mixed $store
     * @return bool
     */
    public function getConfigFlag($path, $store = null);
}

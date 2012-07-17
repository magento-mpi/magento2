<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Store_Config
{
    /**
     * Retrieve store config value
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return Mage::getStoreConfig($path);
    }

    /**
     * Retrieve store config flag
     *
     * @param string $path
     * @return bool
     */
    public function getConfigFlag($path)
    {
        return Mage::getStoreConfigFlag($path);
    }
}

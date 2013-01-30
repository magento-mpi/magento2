<?php
/**
 *  Application state flags
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_App_State
{
    /**
     * Check if application is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
       return Mage::isInstalled();
    }

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return Mage::getIsDeveloperMode();
    }

    /**
     * Set update mode flag
     *
     * @param bool $value
     */
    public function setUpdateMode($value)
    {
        Mage::setUpdateMode($value);
    }

    /**
     * Get update mode flag
     *
     * @return bool
     */
    public function getUpdateMode()
    {
        return Mage::getUpdateMode();
    }

    /**
     * Set is downloader flag
     *
     * @param bool $flag
     */
    public function setIsDownloader($flag = true)
    {
        Mage::setIsDownloader($flag);
    }

    /**
     * Set is serializable flag
     *
     * @param bool $value
     */
    public function setIsSerializable($value = true)
    {
        Mage::setIsSerializable($value);
    }

    /**
     * Get is serializable flag
     *
     * @return bool
     */
    public function getIsSerializable()
    {
        return Mage::getIsSerializable();
    }
}

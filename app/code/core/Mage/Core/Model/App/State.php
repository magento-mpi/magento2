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
     * Return current app mode
     *
     * @return string|null
     */
    public function getMode()
    {
        return Mage::getAppMode();
    }

    /**
     * Set new app mode
     *
     * @param string|null $mode
     * @return string|null
     */
    public function setMode($mode)
    {
        return Mage::setAppMode($mode);
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

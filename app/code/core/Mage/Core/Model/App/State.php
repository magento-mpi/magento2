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
    const XML_PATH_PRODUCTION_MODE = 'global/production_mode';

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
     * Set enabled developer mode
     *
     * @param bool $mode
     * @return bool
     */
    public function setIsDeveloperMode($mode)
    {
        return Mage::setIsDeveloperMode($mode);
    }

    /**
     * Check if production mode is enabled.
     *
     * @return bool
     */
    public function isProductionMode()
    {
        /** @var $config Mage_Core_Model_Config_Primary */
        $config = Mage::getObjectManager()->get('Mage_Core_Model_Config_Primary');
        return (bool)(string)$config->getNode(self::XML_PATH_PRODUCTION_MODE);
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

<?php
/**
 *  Application state flags
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_App_State
{
    /**
     * Application mode
     *
     * @var string
     */
    private $_appMode;

    /**
     * Is downloader flag
     *
     * @var bool
     */
    protected  $_isDownloader = false;

    /**
     * Update mode flag
     *
     * @var bool
     */
    protected  $_updateMode = false;

    /**#@+
     * Application modes
     */
    const MODE_DEVELOPER       = 'developer';
    const MODE_PRODUCTION      = 'production';
    const MODE_DEFAULT         = 'default';
    /**#@-*/

    /**
     * @param string $mode
     * @throws Magento_Core_Exception
     */
    public function __construct($mode = self::MODE_DEFAULT)
    {
        switch ($mode) {
            case self::MODE_DEVELOPER:
            case self::MODE_PRODUCTION:
            case self::MODE_DEFAULT:
                $this->_appMode = $mode;
                break;
            default:
                throw new Magento_Core_Exception("Unknown application mode: {$mode}");
        }
    }

    /**
     * Check if application is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return (bool)Magento_Core_Model_ObjectManager::getInstance()->get('Magento_Core_Model_Config_Primary')
            ->getInstallDate();
    }

    /**
     * Return current app mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_appMode;
    }

    /**
     * Set update mode flag
     *
     * @param bool $value
     */
    public function setUpdateMode($value)
    {
        $this->_updateMode = $value;
    }

    /**
     * Get update mode flag
     *
     * @return bool
     */
    public function getUpdateMode()
    {
        return $this->_updateMode;
    }

    /**
     * Set is downloader flag
     *
     * @param bool $flag
     */
    public function setIsDownloader($flag = true)
    {
        $this->_isDownloader = $flag;
    }
}

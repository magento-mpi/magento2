<?php
/**
 *  Application state flags
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\App;

class State
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

    /**
     * Application install date
     *
     * @var string
     */
    protected $_installDate;

    /**#@+
     * Application modes
     */
    const MODE_DEVELOPER       = 'developer';
    const MODE_PRODUCTION      = 'production';
    const MODE_DEFAULT         = 'default';
    /**#@-*/

    const PARAM_INSTALL_DATE   = 'install.date';

    /**
     * @param string $installDate
     * @param string $mode
     * @throws \Magento\Core\Exception
     */
    public function __construct($installDate, $mode = self::MODE_DEFAULT)
    {
        $this->_installDate = strtotime((string)$installDate);
        switch ($mode) {
            case self::MODE_DEVELOPER:
            case self::MODE_PRODUCTION:
            case self::MODE_DEFAULT:
                $this->_appMode = $mode;
                break;
            default:
                throw new \Magento\Core\Exception("Unknown application mode: {$mode}");
        }
    }

    /**
     * Check if application is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return (bool)$this->_installDate;
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

    /**
     * Set install date
     *
     * @param string $date
     */
    public function setInstallDate($date)
    {
        $this->_installDate = $date;
    }
}

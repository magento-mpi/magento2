<?php
/**
 *  Application state flags
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

use Zend\Soap\Exception\InvalidArgumentException;

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

    /**
     * Config scope model
     *
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * Area code
     *
     * @var string
     */
    protected $_areaCode;

    /**#@+
     * Application modes
     */
    const MODE_DEVELOPER       = 'developer';
    const MODE_PRODUCTION      = 'production';
    const MODE_DEFAULT         = 'default';
    /**#@-*/

    const PARAM_INSTALL_DATE   = 'install.date';

    /**
     * @param \Magento\Config\ScopeInterface $configScope
     * @param string $installDate
     * @param string $mode
     * @throws \LogicException
     */
    public function __construct(\Magento\Config\ScopeInterface $configScope, $installDate, $mode = self::MODE_DEFAULT)
    {
        $this->_installDate = strtotime((string)$installDate);
        $this->_configScope = $configScope;
        switch ($mode) {
            case self::MODE_DEVELOPER:
            case self::MODE_PRODUCTION:
            case self::MODE_DEFAULT:
                $this->_appMode = $mode;
                break;
            default:
                throw new \InvalidArgumentException("Unknown application mode: {$mode}");
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
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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

    /**
     * Set area code
     *
     * @param string $code
     * @throws \Magento\Exception
     */
    public function setAreaCode($code)
    {
        if (isset($this->_areaCode)) {
            throw new \Magento\Exception('Area code is already set');
        }
        $this->_configScope->setCurrentScope($code);
        $this->_areaCode = $code;
    }

    /**
     * Get area code
     *
     * @return string
     * @throws \Magento\Exception
     */
    public function getAreaCode()
    {
        if (!isset($this->_areaCode)) {
            throw new \Magento\Exception('Area code is not set');
        }
        return $this->_areaCode;
    }

    /**
     * Emulate callback inside some area code
     *
     * @param string $areaCode
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    public function emulateAreaCode($areaCode, $callback)
    {
        $currentArea = $this->_areaCode;
        $this->_areaCode = $areaCode;
        try {
            $result = call_user_func($callback);
        } catch (\Exception $e) {
            $this->_areaCode = $currentArea;
            throw $e;
        }
        $this->_areaCode = $currentArea;
        return $result;
    }
}

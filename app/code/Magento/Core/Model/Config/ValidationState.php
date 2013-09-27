<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_ValidationState implements Magento_Config_ValidationStateInterface
{
    /**
     * @var string
     */
    protected $_appMode;

    /**
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->_appMode = $appMode;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->_appMode == Magento_Core_Model_App_State::MODE_DEVELOPER;
    }
}

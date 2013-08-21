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
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(Magento_Core_Model_App_State $appState)
    {
        $this->_appState = $appState;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->_appState->getMode() == Magento_Core_Model_App_State::MODE_DEVELOPER;
    }
}

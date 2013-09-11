<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class ValidationState implements \Magento\Config\ValidationStateInterface
{
    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(\Magento\Core\Model\App\State $appState)
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
        return $this->_appState->getMode() == \Magento\Core\Model\App\State::MODE_DEVELOPER;
    }
}

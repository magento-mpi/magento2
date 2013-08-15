<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Block_Page_Js_Components extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_App_State $appState
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_App_State $appState,
        array $data = array())
    {
        parent::__construct($context, $data);
        $this->_appState = $appState;
    }

    /**
     * @return bool
     */
    public function getIsDeveloperMode()
    {
        return $this->_appState->getMode() == Mage_Core_Model_App_State::MODE_DEVELOPER;
    }
}

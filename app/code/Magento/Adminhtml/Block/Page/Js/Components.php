<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Adminhtml_Block_Page_Js_Components extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_App_State $appState
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_App_State $appState,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_appState = $appState;
    }

    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == Magento_Core_Model_App_State::MODE_DEVELOPER;
    }
}

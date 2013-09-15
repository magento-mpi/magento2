<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Block\Page\Js;

class Components extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param \Magento\Core\Model\App\State $appState
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\App\State $appState,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_appState = $appState;
    }

    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == \Magento\Core\Model\App\State::MODE_DEVELOPER;
    }
}

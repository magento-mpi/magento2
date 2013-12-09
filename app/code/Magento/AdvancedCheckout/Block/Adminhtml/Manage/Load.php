<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

/**
 * Admin Checkout block for returning dynamically loaded content
 */
class Load extends \Magento\View\Block\Template
{
    /**
     * Adminhtml js
     *
     * @var \Magento\Adminhtml\Helper\Js
     */
    protected $_adminhtmlJs;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Adminhtml\Helper\Js $adminhtmlJs
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Adminhtml\Helper\Js $adminhtmlJs,
        array $data = array()
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        parent::__construct($context, $coreData, $data);
    }

    protected function _toHtml()
    {
        $result = array();
        $layout = $this->getLayout();
        foreach ($this->getChildNames() as $name) {
            $result[$name] = $layout->renderElement($name);
        }
        $resultJson = $this->_coreData->jsonEncode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return $this->_adminhtmlJs->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
        } else {
            return $resultJson;
        }
    }
}

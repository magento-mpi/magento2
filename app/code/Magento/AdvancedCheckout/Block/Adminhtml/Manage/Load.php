<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout block for returning dynamically loaded content
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

class Load extends \Magento\View\Element\Template
{
    /*
     * Returns string text with response of loading some blocks
     *
     * @return string
     */
    /**
     * Adminhtml js
     *
     * @var \Magento\Adminhtml\Helper\Js
     */
    protected $_adminhtmlJs = null;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Adminhtml\Helper\Js $adminhtmlJs
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Adminhtml\Helper\Js $adminhtmlJs,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_adminhtmlJs = $adminhtmlJs;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $result = array();
        $layout = $this->getLayout();
        foreach ($this->getChildNames() as $name) {
            $result[$name] = $layout->renderElement($name);
        }
        $resultJson = $this->_jsonEncoder->encode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return $this->_adminhtmlJs->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
        } else {
            return $resultJson;
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Centinel validation form lookup
 */
namespace Magento\Centinel\Block\Authentication;

class Complete extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare authentication result params and render
     *
     * @return string
     */
    protected function _toHtml()
    {
        $validator = $this->_coreRegistry->registry('current_centinel_validator');
        if ($validator) {
            $this->setIsProcessed(true);
            $this->setIsSuccess($validator->isAuthenticateSuccessful());
        }
        return parent::_toHtml();
    }
}

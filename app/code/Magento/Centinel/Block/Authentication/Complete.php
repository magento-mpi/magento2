<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Centinel validation form lookup
 */
namespace Magento\Centinel\Block\Authentication;

class Complete extends \Magento\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
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


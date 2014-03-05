<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block;

/**
 * "Checkout" link
 */
class Link extends \Magento\View\Element\Html\Link
{
    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Module\Manager $moduleManager,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        array $data = array()
    ) {
        $this->_checkoutHelper = $checkoutHelper;
        parent::__construct($context, $data);
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout', array('_secure' => true));
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_checkoutHelper->canOnepageCheckout()
            || !$this->_moduleManager->isOutputEnabled('Magento_Checkout')
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}

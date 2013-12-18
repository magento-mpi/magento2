<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "My Cart" link
 */
namespace Magento\Checkout\Block\Cart;

class Link extends \Magento\View\Element\Html\Link
{
    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Module\Manager $moduleManager,
        \Magento\Checkout\Helper\Cart $cartHelper,
        array $data = array()
    ) {
        $this->_cartHelper = $cartHelper;
        parent::__construct($context, $data);
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_createLabel($this->_getItemCount());
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout/cart');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_moduleManager->isOutputEnabled('Magento_Checkout')) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Count items in cart
     *
     * @return int
     */
    protected function _getItemCount()
    {
        $count = $this->getSummaryQty();
        return $count ? $count : $this->_cartHelper->getSummaryCount();
    }

    /**
     * Create link label based on cart item quantity
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($count == 1) {
            return __('My Cart (%1 item)', $count);
        } elseif ($count > 0) {
            return __('My Cart (%1 items)', $count);
        } else {
            return __('My Cart');
        }
    }
}

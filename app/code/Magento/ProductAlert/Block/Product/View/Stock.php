<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Product\View;

/**
 * Recurring payment view stock
 */
class Stock extends \Magento\ProductAlert\Block\Product\View
{
    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (!$this->_helper->isStockAlertAllowed() || !$this->_product || $this->_product->isAvailable()) {
            $template = '';
        } else {
            $this->setSignupUrl($this->_helper->getSaveUrl('stock'));
        }
        return parent::setTemplate($template);
    }
}

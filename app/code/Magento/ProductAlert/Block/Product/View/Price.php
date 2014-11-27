<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Product\View;

/**
 * Product view price
 */
class Price extends \Magento\ProductAlert\Block\Product\View
{
    /**
     * Prepare price info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (
            !$this->_helper->isPriceAlertAllowed()
            || !$this->getProduct() ||
            false === $this->getProduct()->getCanShowPrice()
        ) {
            $template = '';
        } else {
            $this->setSignupUrl($this->_helper->getSaveUrl('price'));
        }
        return parent::setTemplate($template);
    }
}

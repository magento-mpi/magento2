<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

class GetAdditional extends \Magento\Checkout\Controller\Onepage
{
    /**
     * @return string
     */
    protected function _getAdditionalHtml()
    {
        return $this->_getHtmlByHandle('checkout_onepage_additional');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody($this->_getAdditionalHtml());
    }
}

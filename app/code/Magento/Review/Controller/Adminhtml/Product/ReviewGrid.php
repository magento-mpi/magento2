<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml\Product;

class ReviewGrid extends \Magento\Review\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock('Magento\Review\Block\Adminhtml\Grid')->toHtml()
        );
    }
}

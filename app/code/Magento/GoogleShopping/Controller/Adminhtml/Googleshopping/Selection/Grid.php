<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Selection;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * Grid with available products for Google Content
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\GoogleShopping\Block\Adminhtml\Items\Product'
            )->setIndex(
                $this->getRequest()->getParam('index')
            )->toHtml()
        );
    }
}

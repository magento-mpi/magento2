<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Template;

class Grid extends \Magento\Newsletter\Controller\Adminhtml\Template
{
    /**
     * JSON Grid Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $grid = $this->_view->getLayout()->createBlock('Magento\Newsletter\Block\Adminhtml\Template\Grid')->toHtml();
        $this->getResponse()->setBody($grid);
    }
}

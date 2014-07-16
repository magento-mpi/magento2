<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml\Product;

class Edit extends \Magento\Review\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Reviews'));

        $this->_title->add(__('Edit Review'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_reviews_all');

        $this->_addContent($this->_view->getLayout()->createBlock('Magento\Review\Block\Adminhtml\Edit'));

        $this->_view->renderLayout();
    }
}

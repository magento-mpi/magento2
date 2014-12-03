<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml\Rating;

class Index extends \Magento\Review\Controller\Adminhtml\Rating
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initEnityId();
        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Ratings'));
        $this->_view->renderLayout();
    }
}

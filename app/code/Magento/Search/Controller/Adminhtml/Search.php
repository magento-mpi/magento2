<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Search extends \Magento\Backend\App\Action
{
    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CatalogSearch::catalog_search')->_addBreadcrumb(__('Search'), __('Search'));
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogSearch::search');
    }
}

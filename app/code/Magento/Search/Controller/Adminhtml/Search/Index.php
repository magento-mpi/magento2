<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Search;

class Index extends \Magento\Search\Controller\Adminhtml\Search
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Search Terms'));

        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->renderLayout();
    }
}

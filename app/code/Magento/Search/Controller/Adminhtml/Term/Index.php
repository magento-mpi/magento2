<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Term;

class Index extends \Magento\Search\Controller\Adminhtml\Term
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Search Terms'));

        $this->_initAction()->_addBreadcrumb(__('Search'), __('Search'));
        $this->_view->renderLayout();
    }
}

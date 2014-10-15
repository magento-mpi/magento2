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
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_title->add(__('Search Terms'));

        $resultPage = $this->createPage();
        $resultPage->addBreadcrumb(__('Search'), __('Search'));
        return $resultPage;
    }
}

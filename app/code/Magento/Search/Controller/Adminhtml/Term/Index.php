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
        $resultPage = $this->createPage();
        $resultPage->getPage()->getConfig()->getTitle()->prepend(__('Search Terms'));
        $resultPage->addBreadcrumb(__('Search'), __('Search'));
        return $resultPage;
    }
}

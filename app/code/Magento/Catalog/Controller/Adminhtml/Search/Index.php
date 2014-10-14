<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Search;

class Index extends \Magento\Catalog\Controller\Adminhtml\Search
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_title->add(__('Search Terms'));

        $resultPage = $this->createPage();
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'));
        return $resultPage;
    }
}

<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class Preview extends \Magento\Backend\App\Action implements RevisionInterface
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     */
    public function __construct(Action\Context $context, \Magento\VersionsCms\Model\PageLoader $pageLoader)
    {
        $this->pageLoader = $pageLoader;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Prepares page with iframe
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (empty($data) || !isset($data['page_id'])) {
            $this->_forward('noroute');
            return;
        }

        $page = $this->pageLoader->load($this->_request->getParam('page_id'));
        $this->_view->loadLayout();

        $stores = $page->getStoreId();
        if (isset($data['stores'])) {
            $stores = $data['stores'];
        }

        /*
         * Checking if all stores passed then we should not assign array to block
         */
        $allStores = false;
        if (is_array($stores) && count($stores) == 1 && !array_shift($stores)) {
            $allStores = true;
        }

        if (!$allStores) {
            $this->_view->getLayout()->getBlock('store_switcher')->setStoreIds($stores);
        }

        // Setting default values for selected store and revision
        $data['preview_selected_store'] = 0;
        $data['preview_selected_revision'] = 0;

        $this->_view->getLayout()->getBlock('preview_form')->setFormData($data);

        // Remove revision switcher if page is out of version control
        if (!$page->getUnderVersionControl()) {
            $this->_view->getLayout()->unsetChild('tools', 'revision_switcher');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pages'));
        $this->_view->renderLayout();
    }
}

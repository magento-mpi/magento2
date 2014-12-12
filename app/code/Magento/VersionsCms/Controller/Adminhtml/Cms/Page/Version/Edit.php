<?php
/**
 * Edit version of CMS page
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class Edit extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Edit
{
    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param VersionProvider $versionProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        VersionProvider $versionProvider
    ) {
        $this->versionProvider = $versionProvider;
        parent::__construct($context, $registry, $pageLoader, $cmsConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Cms::cms_page'
        )->_addBreadcrumb(
            __('CMS'),
            __('CMS')
        )->_addBreadcrumb(
            __('Manage Pages'),
            __('Manage Pages')
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $version = $this->versionProvider->get($this->_request->getParam('version_id'));

        if (!$version->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));
            $this->_redirect('adminhtml/cms_page/edit', ['page_id' => $this->getRequest()->getParam('page_id')]);
            return;
        }

        $this->pageLoader->load($this->_request->getParam('page_id'));

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $version->getData();
            $_data = array_merge($_data, $data);
            $version->setData($_data);
        }

        $this->_initAction()->_addBreadcrumb(__('Edit Version'), __('Edit Version'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pages'));
        $this->_view->renderLayout();
    }
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Backend\App\Action;

class Edit extends \Magento\Cms\Controller\Adminhtml\Page\Edit
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @var array
     */
    protected $_handles = array();

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        \Magento\VersionsCms\Model\Config $cmsConfig
    ) {
        $this->pageLoader = $pageLoader;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $registry);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        $update = $this->_view->getLayout()->getUpdate();
        $update->addHandle('default');

        // add default layout handles for this action
        $this->_view->addActionLayoutHandles();
        $update->addHandle($this->_handles);

        //load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_VersionsCms::versionscms_page_page'
        )->_addBreadcrumb(
            __('CMS'),
            __('CMS')
        )->_addBreadcrumb(
            __('Manage Pages'),
            __('Manage Pages')
        );

        $this->_view->setIsLayoutLoaded(true);

        return $this;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Pages'));
        $page = $this->pageLoader->load($this->_request->getParam('page_id'));

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $page->setData($data);
        }

        if ($page->getId()) {
            if ($page->getUnderVersionControl()) {
                $this->_handles[] = 'adminhtml_cms_page_edit_changes';
            }
        } elseif (!$page->hasUnderVersionControl()) {
            $page->setUnderVersionControl((int)$this->_cmsConfig->getDefaultVersioningStatus());
        }

        $this->_title->add($page->getId() ? $page->getTitle() : __('New Page'));

        $this->_initAction()->_addBreadcrumb(
            $page->getId() ? __('Edit Page') : __('New Page'),
            $page->getId() ? __('Edit Page') : __('New Page')
        );

        $this->_view->renderLayout();
    }
}

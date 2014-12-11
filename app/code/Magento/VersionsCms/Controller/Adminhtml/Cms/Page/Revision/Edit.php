<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class Edit extends \Magento\Cms\Controller\Adminhtml\Page\Edit implements RevisionInterface
{
    /**
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param RevisionProvider $revisionProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        RevisionProvider $revisionProvider
    ) {
        $this->revisionProvider = $revisionProvider;
        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Edit revision of CMS page
     *
     * @return void
     */
    public function execute()
    {
        $revisionId = $this->getRequest()->getParam('revision_id');
        $revision = $this->revisionProvider->get($revisionId, $this->_request);

        if ($revisionId && !$revision->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));

            $this->_redirect('adminhtml/cms_page/edit', ['page_id' => $this->getRequest()->getParam('page_id')]);
            return;
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $revision->getData();
            $_data = array_merge($_data, $data);
            $revision->setData($_data);
        }

        $this->_initAction()->_addBreadcrumb(__('Edit Revision'), __('Edit Revision'));
        $this->_view->renderLayout();
    }
}

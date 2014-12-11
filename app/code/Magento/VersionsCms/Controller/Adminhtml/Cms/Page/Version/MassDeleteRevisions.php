<?php
/**
 * Mass deletion for revisions
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class MassDeleteRevisions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\VersionsCms\Model\Page\Revision
     */
    protected $_pageRevision;

    /**
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\Page\Revision $pageRevision
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\Page\Revision $pageRevision
    ) {
        $this->_backendAuthSession = $backendSession;
        $this->_cmsConfig = $cmsConfig;
        $this->_pageRevision = $pageRevision;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_cmsConfig->canCurrentUserDeleteRevision();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('revision');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select revision(s).'));
        } else {
            try {
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $revision = $this->_pageRevision->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($revision->getId()) {
                        $revision->delete();
                    }
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($ids)));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting the revisions.'));
            }
        }
        $this->_redirect('adminhtml/*/edit', ['_current' => true, 'tab' => 'revisions']);
    }
}

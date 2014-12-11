<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Backend\App\Action;

class MassDeleteVersions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\VersionsCms\Model\Page\Version
     */
    protected $pageVersion;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $cmsConfig;

    /**
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\Page\Version $pageVersion
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\Page\Version $pageVersion
    ) {
        $this->backendAuthSession = $backendSession;
        $this->cmsConfig = $cmsConfig;
        $this->pageVersion = $pageVersion;
        parent::__construct($context);
    }

    /**
     * Mass deletion for versions
     *
     * @return void
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('version');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select version(s).'));
        } else {
            try {
                $userId = $this->backendAuthSession->getUser()->getId();
                $accessLevel = $this->cmsConfig->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $version = $this->pageVersion->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($version->getId()) {
                        $version->delete();
                    }
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($ids)));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting these versions.'));
            }
        }
        $this->_redirect('adminhtml/*/edit', ['_current' => true, 'tab' => 'versions']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->cmsConfig->canCurrentUserDeleteVersion();
    }
}

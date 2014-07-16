<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Cms\Controller\Adminhtml\Page;

class MassDeleteVersions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\VersionsCms\Model\Page\Version
     */
    protected $_pageVersion;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

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
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $version = $this->_pageVersion->loadWithRestrictions($accessLevel, $userId, $id);

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
        $this->_redirect('adminhtml/*/edit', array('_current' => true, 'tab' => 'versions'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_cmsConfig->canCurrentUserDeleteVersion();
    }
}

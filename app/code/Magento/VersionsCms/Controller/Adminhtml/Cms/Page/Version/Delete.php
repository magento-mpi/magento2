<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class Delete extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Delete
{
    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param Action\Context $context
     * @param VersionProvider $versionProvider
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     */
    public function __construct(
        Action\Context $context,
        VersionProvider $versionProvider,
        \Magento\VersionsCms\Model\Config $cmsConfig
    ) {
        $this->versionProvider = $versionProvider;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_cmsConfig->canCurrentUserDeleteVersion();
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('version_id');
        if ($id) {
            // init model
            $version = $this->versionProvider->get($this->_request->getParam('version_id'));
            $error = false;
            try {
                $version->delete();
                // display success message
                $this->messageManager->addSuccess(__('You have deleted the version.'));
                $this->_redirect('adminhtml/cms_page/edit', ['page_id' => $version->getPageId()]);
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting this version.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__("We can't find a version to delete."));
        // go to grid
        $this->_redirect('adminhtml/cms_page/edit', ['_current' => true]);
    }
}

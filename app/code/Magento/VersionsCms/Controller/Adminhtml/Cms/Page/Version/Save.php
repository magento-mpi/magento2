<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;
use Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor;

class Save extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Save
{
    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param VersionProvider $versionProvider
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        VersionProvider $versionProvider
    ) {
        $this->versionProvider = $versionProvider;
        parent::__construct($context, $dataProcessor);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::save_revision');
    }

    /**
     * Save Action
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $version = $this->versionProvider->get($this->_request->getParam('version_id'));

            // if current user not publisher he can't change owner
            if (!$this->_cmsConfig->canCurrentUserPublishRevision()) {
                unset($data['user_id']);
            }
            $version->addData($data);

            // try to save it
            try {
                // save the data
                $version->save();

                // display success message
                $this->messageManager->addSuccess(__('You have saved the version.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        'adminhtml/*/' . $this->getRequest()->getParam('back'),
                        array('page_id' => $version->getPageId(), 'version_id' => $version->getId())
                    );
                    return;
                }
                // go to grid
                $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $version->getPageId()));
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_session->setFormData($data);
                // redirect to edit form
                $this->_redirect(
                    'adminhtml/*/edit',
                    array(
                        'page_id' => $this->getRequest()->getParam('page_id'),
                        'version_id' => $this->getRequest()->getParam('version_id')
                    )
                );
                return;
            }
        }
    }
}

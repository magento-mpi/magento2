<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml\Page;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            //init model and set data
            $model = $this->_objectManager->create('Magento\Cms\Model\Page');

            $id = $this->getRequest()->getParam('page_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'cms_page_prepare_save',
                array('page' => $model, 'request' => $this->getRequest())
            );

            //validating
            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                $this->messageManager->addSuccess(__('The page has been saved.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current' => true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}

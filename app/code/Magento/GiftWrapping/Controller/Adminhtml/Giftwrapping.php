<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Controller
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Giftwrapping extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init active menu
     *
     * @return \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_GiftWrapping::sales_magento_giftwrapping');

        $this->_title->add(__('Gift Wrapping'));
        return $this;
    }

    /**
     * Init model
     *
     * @param string $requestParam
     * @return \Magento\GiftWrapping\Model\Wrapping
     * @throws \Magento\Core\Exception
     */
    protected function _initModel($requestParam = 'id')
    {
        $model = $this->_coreRegistry->registry('current_giftwrapping_model');
        if ($model) {
           return $model;
        }
        $model = $this->_objectManager->create('Magento\GiftWrapping\Model\Wrapping');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $wrappingId = $this->getRequest()->getParam($requestParam);
        if ($wrappingId) {
            $model->load($wrappingId);
            if (!$model->getId()) {
                throw new \Magento\Core\Exception(__('Please request the correct gift wrapping.'));
            }
        }
        $this->_coreRegistry->register('current_giftwrapping_model', $model);

        return $model;
    }

    /**
     * List of gift wrappings
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Create new gift wrapping
     */
    public function newAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $this->_title->add(__('New Gift Wrapping'));
        $this->_view->renderLayout();
    }

    /**
     * Edit gift wrapping
     */
    public function editAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData();
        if ($formData) {
            $model->addData($formData);
        }
        $this->_title->add(__('%1', $model->getDesign()));
        $this->_view->renderLayout();
    }

    /**
     * Save gift wrapping
     */
    public function saveAction()
    {
        $wrappingRawData = $this->_prepareGiftWrappingRawData($this->getRequest()->getPost('wrapping'));
        if ($wrappingRawData) {
            try {
                $model = $this->_initModel();
                $model->addData($wrappingRawData);

                $data = new \Magento\Object($wrappingRawData);
                if ($data->getData('image_name/delete')) {
                    $model->setImage('');
                    // Delete temporary image if exists
                    $model->unsTmpImage();
                } else {
                    try {
                        $model->attachUploadedImage('image_name');
                    } catch (\Exception $e) {
                        throw new \Magento\Core\Exception(__('You have not uploaded the image.'));
                    }
                }

                $model->save();
                $this->messageManager->addSuccess(__('You saved the gift wrapping.'));

                $redirectBack = $this->getRequest()->getParam('back', false);
                if ($redirectBack) {
                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId(), 'store' => $model->getStoreId()));
                    return;
                }
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save the gift wrapping."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Upload temporary gift wrapping image
     */
    public function uploadAction()
    {
        $wrappingRawData = $this->_prepareGiftWrappingRawData($this->getRequest()->getPost('wrapping'));
        if ($wrappingRawData) {
            try {
                $model = $this->_initModel();
                $model->addData($wrappingRawData);
                try {
                    $model->attachUploadedImage('image_name', true);
                } catch (\Exception $e) {
                    throw new \Magento\Core\Exception(__('You have not updated the image.'));
                }
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($wrappingRawData);
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save the gift wrapping."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }

        if (isset($model) && $model->getId()) {
            $this->_forward('edit');
        } else {
            $this->_forward('new');
        }
    }

    /**
     * Change gift wrapping(s) status action
     */
    public function changeStatusAction()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        $status = (int)(bool)$this->getRequest()->getParam('status');
        try {
            $wrappingCollection = $this->_objectManager
                ->create('Magento\GiftWrapping\Model\Resource\Wrapping\Collection');
            $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
            foreach ($wrappingCollection as $wrapping) {
                $wrapping->setStatus($status);
            }
            $wrappingCollection->save();
            $this->messageManager->addSuccess(
                __('You updated a total of %1 records.', count($wrappingIds))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while updating the wrapping(s) status.'));
        }

        $this->_redirect('adminhtml/*/index');
    }

    /**
     * Delete specified gift wrapping(s)
     * This action can be performed on 'Manage Gift Wrappings' page
     */
    public function massDeleteAction()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        if (!is_array($wrappingIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $wrappingCollection = $this->_objectManager
                    ->create('Magento\GiftWrapping\Model\Resource\Wrapping\Collection');
                $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
                foreach ($wrappingCollection as $wrapping) {
                    $wrapping->delete();
                }
                $this->messageManager->addSuccess(
                    __('You deleted a total of %1 records.', count($wrappingIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/*/index');
    }

    /**
     * Delete current gift wrapping
     * This action can be performed on 'Edit Gift Wrapping' page
     */
    public function deleteAction()
    {
        $wrapping = $this->_objectManager->create('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->load($this->getRequest()->getParam('id', false));
        if ($wrapping->getId()) {
            try {
                $wrapping->delete();
                $this->messageManager->addSuccess(__('You deleted the gift wrapping.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current'=>true));
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftWrapping::magento_giftwrapping');
    }

    /**
     * Prepare Gift Wrapping Raw data
     *
     * @param array $wrappingRawData
     * @return array
     */
    protected function _prepareGiftWrappingRawData($wrappingRawData)
    {
        if (isset($wrappingRawData['tmp_image'])) {
            $wrappingRawData['tmp_image'] = basename($wrappingRawData['tmp_image']);
        }
        if (isset($wrappingRawData['image_name']['value'])) {
            $wrappingRawData['image_name']['value'] = basename($wrappingRawData['image_name']['value']);
        }
        return $wrappingRawData;
    }

    /**
     * Ajax action for GiftWrapping content in backend order creation
     *
     * @deprecated since 1.12.0.0
     */
    public function orderOptionsAction() {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}

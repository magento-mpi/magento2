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

class Giftwrapping extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
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
        $this->loadLayout()->_setActiveMenu('Magento_GiftWrapping::sales_magento_giftwrapping');

        $this->_title(__('Gift Wrapping'));
        return $this;
    }

    /**
     * Init model
     *
     * @return Magento_Giftwrapping_Model_Wrapping
     */
    protected function _initModel($requestParam = 'id')
    {
        $model = $this->_coreRegistry->registry('current_giftwrapping_model');
        if ($model) {
           return $model;
        }
        $model = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $wrappingId = $this->getRequest()->getParam($requestParam);
        if ($wrappingId) {
            $model->load($wrappingId);
            if (!$model->getId()) {
                \Mage::throwException(__('Please request the correct gift wrapping.'));
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
        $this->_initAction()->renderLayout();
    }

    /**
     * Create new gift wrapping
     */
    public function newAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $this->_title(__('New Gift Wrapping'));
        $this->renderLayout();
    }

    /**
     * Edit gift wrapping
     */
    public function editAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $formData = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData();
        if ($formData) {
            $model->addData($formData);
        }
        $this->_title(__('%1', $model->getDesign()));
        $this->renderLayout();
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
                        \Mage::throwException(__('You have not uploaded the image.'));
                    }
                }

                $model->save();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the gift wrapping.'));

                $redirectBack = $this->getRequest()->getParam('back', false);
                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $model->getStoreId()));
                    return;
                }
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__("We couldn't save the gift wrapping."));
                \Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
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
                    \Mage::throwException(__('You have not updated the image.'));
                }
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_getSession()->setFormData($wrappingRawData);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__("We couldn't save the gift wrapping."));
                \Mage::logException($e);
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
            $wrappingCollection = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping')->getCollection();
            $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
            foreach ($wrappingCollection as $wrapping) {
                $wrapping->setStatus($status);
            }
            $wrappingCollection->save();
            $this->_getSession()->addSuccess(
                __('You updated a total of %1 records.', count($wrappingIds))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the wrapping(s) status.'));
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Delete specified gift wrapping(s)
     * This action can be performed on 'Manage Gift Wrappings' page
     */
    public function massDeleteAction()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        if (!is_array($wrappingIds)) {
            $this->_getSession()->addError(__('Please select items.'));
        } else {
            try {
                $wrappingCollection = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping')->getCollection();
                $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
                foreach ($wrappingCollection as $wrapping) {
                    $wrapping->delete();
                }
                $this->_getSession()->addSuccess(
                    __('You deleted a total of %1 records.', count($wrappingIds))
                );
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Delete current gift wrapping
     * This action can be performed on 'Edit Gift Wrapping' page
     */
    public function deleteAction()
    {
        $wrapping = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->load($this->getRequest()->getParam('id', false));
        if ($wrapping->getId()) {
            try {
                $wrapping->delete();
                $this->_getSession()->addSuccess(__('You deleted the gift wrapping.'));
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current'=>true));
            }
        }
        $this->_redirect('*/*/');
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
        $this->loadLayout();
        $this->renderLayout();
    }
}

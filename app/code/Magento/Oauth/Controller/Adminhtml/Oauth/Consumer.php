<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Manage consumers controller
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Controller_Adminhtml_Oauth_Consumer extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Perform layout initialization actions
     *
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_Consumer
     */
    protected function  _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Oauth::system_legacy_api_oauth_consumer');
        return $this;
    }
    /**
     * Unset unused data from request
     * Skip getting "key" and "secret" because its generated from server side only
     *
     * @param array $data
     * @return array
     */
    protected function _filter(array $data)
    {
        foreach (array('id', 'back', 'form_key', 'key', 'secret') as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        return $data;
    }

    /**
     * Init titles
     *
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_Consumer
     */
    public function preDispatch()
    {
        $this->_title(__('Consumers'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Render grid AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create page action
     */
    public function newAction()
    {
        /** @var $model Magento_Oauth_Model_Consumer */
        $model = Mage::getModel('Magento_Oauth_Model_Consumer');

        $formData = $this->_getFormData();
        if ($formData) {
            $this->_setFormData($formData);
            $model->addData($formData);
        } else {
            /** @var $helper Magento_Oauth_Helper_Data */
            $helper = Mage::helper('Magento_Oauth_Helper_Data');
            $model->setKey($helper->generateConsumerKey());
            $model->setSecret($helper->generateConsumerSecret());
            $this->_setFormData($model->getData());
        }

        $this->_coreRegistry->register('current_consumer', $model);

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Edit page action
     */
    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_getSession()->addError(__('Invalid ID parameter.'));
            $this->_redirect('*/*/index');
            return;
        }

        /** @var $model Magento_Oauth_Model_Consumer */
        $model = Mage::getModel('Magento_Oauth_Model_Consumer');
        $model->load($id);

        if (!$model->getId()) {
            $this->_getSession()->addError(__('Entry with ID #%1 not found.', $id));
            $this->_redirect('*/*/index');
            return;
        }

        $model->addData($this->_filter($this->getRequest()->getParams()));
        $this->_coreRegistry->register('current_consumer', $model);

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Render edit page
     */
    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$this->_validateFormKey()) {
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirect('*/*/new', array('id' => $id));
            }
            return;
        }

        $data = $this->_filter($this->getRequest()->getParams());

        /** @var $model Magento_Oauth_Model_Consumer */
        $model = Mage::getModel('Magento_Oauth_Model_Consumer');

        if ($id) {
            if (!(int) $id) {
                $this->_getSession()->addError(
                    __('Invalid ID parameter.'));
                $this->_redirect('*/*/index');
                return;
            }
            $model->load($id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    __('Entry with ID #%1 not found.', $id));
                $this->_redirect('*/*/index');
                return;
            }
        } else {
            $dataForm = $this->_getFormData();
            if ($dataForm) {
                $data['key']    = $dataForm['key'];
                $data['secret'] = $dataForm['secret'];
            } else {
                // If an admin was started create a new consumer and at this moment he has been edited an existing
                // consumer, we save the new consumer with a new key-secret pair
                /** @var $helper Magento_Oauth_Helper_Data */
                $helper = Mage::helper('Magento_Oauth_Helper_Data');

                $data['key']    = $helper->generateConsumerKey();
                $data['secret'] = $helper->generateConsumerSecret();
            }
        }

        try {
            $model->addData($data);
            $model->save();
            $this->_getSession()->addSuccess(__('The consumer has been saved.'));
            $this->_setFormData(null);
        } catch (Magento_Core_Exception $e) {
            $this->_setFormData($data);
            $this->_getSession()->addError(Mage::helper('Magento_Core_Helper_Data')->escapeHtml($e->getMessage()));
            $this->getRequest()->setParam('back', 'edit');
        } catch (Exception $e) {
            $this->_setFormData(null);
            Mage::logException($e);
            $this->_getSession()->addError(__('An error occurred on saving consumer data.'));
        }

        if ($this->getRequest()->getParam('back')) {
            if ($id || $model->getId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            } else {
                $this->_redirect('*/*/new');
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        $resourceId = null;
        switch ($action) {
            case 'delete':
                $resourceId = 'Magento_Oauth::consumer_delete';
                break;

            case 'new':
            case 'save':
                $resourceId = 'Magento_Oauth::consumer_edit';
                break;

            default:
                $resourceId = 'Magento_Oauth::consumer';
                break;
        }

        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get form data
     *
     * @return array
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('consumer_data', true);
    }

    /**
     * Set form data
     *
     * @param $data
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_Consumer
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('consumer_data', $data);
        return $this;
    }

    /**
     * Delete consumer action
     */
    public function deleteAction()
    {
        $consumerId = (int) $this->getRequest()->getParam('id');
        if ($consumerId) {
            try {
                /** @var $consumer Magento_Oauth_Model_Consumer */
                $consumer = Mage::getModel('Magento_Oauth_Model_Consumer')->load($consumerId);
                if (!$consumer->getId()) {
                    Mage::throwException(__('Unable to find a consumer.'));
                }

                $consumer->delete();

                $this->_getSession()->addSuccess(__('The consumer has been deleted.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e, __('An error occurred while deleting the consumer.')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
}

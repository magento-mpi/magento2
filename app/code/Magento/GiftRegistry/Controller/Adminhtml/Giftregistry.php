<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Controller_Adminhtml_Giftregistry extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init active menu and set breadcrumb
     *
     * @return Magento_GiftRegistry_Controller_Adminhtml_Giftregistry
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_GiftRegistry::customer_magento_giftregistry')
            ->_addBreadcrumb(
                __('Gift Registry'),
                __('Gift Registry')
            );

        $this->_title(__('Gift Registry Types'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return Magento_GiftRegistry_Model_Type
     */
    protected function _initType($requestParam = 'id')
    {
        $type = Mage::getModel('Magento_GiftRegistry_Model_Type');
        $type->setStoreId($this->getRequest()->getParam('store', 0));

        if ($typeId = $this->getRequest()->getParam($requestParam)) {
            $type->load($typeId);
            if (!$type->getId()) {
                Mage::throwException(__('Please correct the  gift registry ID.'));
            }
        }
        Mage::register('current_giftregistry_type', $type);
        return $type;
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /**
     * Create new gift registry type
     */
    public function newAction()
    {
        try {
            $model = $this->_initType();
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }

        $this->_initAction();
        $this->_title(__('New Gift Registry Type'));

        $block = $this->getLayout()->createBlock('Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));

        $this->_addBreadcrumb(__('New Type'), __('New Type'))
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock(
                'Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs')
            )
            ->renderLayout();
    }

    /**
     * Edit gift registry type
     */
    public function editAction()
    {
        try {
            $model = $this->_initType();
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }

        $this->_initAction();
        $this->_title(__('%1', $model->getLabel()));

        $block = $this->getLayout()->createBlock('Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));

        $this->_addBreadcrumb(__('Edit Type'), __('Edit Type'))
            ->_addContent($block)
            ->_addLeft(
                $this->getLayout()->createBlock('Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs')
            )
            ->renderLayout();
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        $helper = $this->_getHelper();
        if (!empty($data['type']['label'])) {
            $data['type']['label'] = $helper->stripTags($data['type']['label']);
        }
        if (!empty($data['attributes']['registry'])) {
            foreach ($data['attributes']['registry'] as &$regItem) {
                if (!empty($regItem['label'])) {
                    $regItem['label'] = $helper->stripTags($regItem['label']);
                }
                if (!empty($regItem['options'])) {
                    foreach ($regItem['options'] as &$option) {
                        $option['label'] = $helper->stripTags($option['label']);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Save gift registry type
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            //filtering
            $data = $this->_filterPostData($data);
            try {
                $model = $this->_initType();
                $model->loadPost($data);
                $model->save();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                        ->addSuccess(__('You saved the gift registry type.'));

                if ($redirectBack = $this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $model->getStoreId()));
                    return;
                }
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__("We couldn't save this gift registry type."));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete gift registry type
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initType();
            $model->delete();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You deleted the gift registry type.'));
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__("We couldn't delete this gift registry type."));
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}

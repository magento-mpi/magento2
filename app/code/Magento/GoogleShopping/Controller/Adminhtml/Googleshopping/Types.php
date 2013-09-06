<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Admin Item Types Controller
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @name       Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Types
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Types extends Magento_Adminhtml_Controller_Action
{
    /**
     * Dispatches controller_action_postdispatch_adminhtml Event (as not Adminhtml router)
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if ($this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
            return;
        }
        $this->_eventManager->dispatch('controller_action_postdispatch_adminhtml', array('controller_action' => $this));
    }

    /**
     * Initialize attribute set mapping object
     *
     * @return Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Types
     */
    protected function _initItemType()
    {
        $this->_title(__('Google Content Attributes'));

        Mage::register('current_item_type', Mage::getModel('Magento_GoogleShopping_Model_Type'));
        $typeId = $this->getRequest()->getParam('id');
        if (!is_null($typeId)) {
            Mage::registry('current_item_type')->load($typeId);
        }
        return $this;
    }

    /**
     * Initialize general settings for action
     *
     * @return  Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Items
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_GoogleShopping::catalog_googleshopping_types')
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addBreadcrumb(__('Google Content'), __('Google Content'));
        return $this;
    }

    /**
     * List of all maps (items)
     */
    public function indexAction()
    {
        $this->_title(__('Google Content Attributes'));

        $this->_initAction()
            ->_addBreadcrumb(__('Attribute Maps'), __('Attribute Maps'))
            ->renderLayout();
    }

    /**
     * Grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout('false');
        $this->renderLayout();
    }

    /**
     * Create new attribute set mapping
     */
    public function newAction()
    {
        try {
            $this->_initItemType();

            $this->_title(__('New Google Content Attribute Mapping'));

            $this->_initAction()
                ->_addBreadcrumb(__('New attribute set mapping'), __('New attribute set mapping'))
                ->_addContent($this->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Types_Edit'))
                ->renderLayout();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_getSession()->addError(__("We can't create Attribute Set Mapping."));
            $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
        }
    }

    /**
     * Edit attribute set mapping
     */
    public function editAction()
    {
        $this->_initItemType();
        $typeId = Mage::registry('current_item_type')->getTypeId();

        try {
            $result = array();
            if ($typeId) {
                $collection = Mage::getResourceModel('Magento_GoogleShopping_Model_Resource_Attribute_Collection')
                    ->addTypeFilter($typeId)
                    ->load();
                foreach ($collection as $attribute) {
                    $result[] = $attribute->getData();
                }
            }

            $this->_title(__('Google Content Attribute Mapping'));
            Mage::register('attributes', $result);

            $breadcrumbLabel = $typeId ? __('Edit attribute set mapping') : __('New attribute set mapping');
            $this->_initAction()
                ->_addBreadcrumb($breadcrumbLabel, $breadcrumbLabel)
                ->_addContent($this->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Types_Edit'))
                ->renderLayout();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_getSession()->addError(__("We can't edit Attribute Set Mapping."));
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Save attribute set mapping
     */
    public function saveAction()
    {
        /** @var $typeModel Magento_GoogleShopping_Model_Type */
        $typeModel = Mage::getModel('Magento_GoogleShopping_Model_Type');
        $id = $this->getRequest()->getParam('type_id');
        if (!is_null($id)) {
            $typeModel->load($id);
        }

        try {
            $typeModel->setCategory($this->getRequest()->getParam('category'));
            if ($typeModel->getId()) {
                $collection = Mage::getResourceModel('Magento_GoogleShopping_Model_Resource_Attribute_Collection')
                    ->addTypeFilter($typeModel->getId())
                    ->load();
                foreach ($collection as $attribute) {
                    $attribute->delete();
                }
            } else {
                $typeModel->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'))
                    ->setTargetCountry($this->getRequest()->getParam('target_country'));
            }
            $typeModel->save();

            $attributes = $this->getRequest()->getParam('attributes');
            $requiredAttributes = Mage::getSingleton('Magento_GoogleShopping_Model_Config')->getRequiredAttributes();
            if (is_array($attributes)) {
                $typeId = $typeModel->getId();
                foreach ($attributes as $attrInfo) {
                    if (isset($attrInfo['delete']) && $attrInfo['delete'] == 1) {
                        continue;
                    }
                    Mage::getModel('Magento_GoogleShopping_Model_Attribute')
                        ->setAttributeId($attrInfo['attribute_id'])
                        ->setGcontentAttribute($attrInfo['gcontent_attribute'])
                        ->setTypeId($typeId)
                        ->save();
                    unset($requiredAttributes[$attrInfo['gcontent_attribute']]);
                }
            }

            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The attribute mapping has been saved.'));
            if (!empty($requiredAttributes)) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addSuccess(Mage::helper('Magento_GoogleShopping_Helper_Category')->getMessage());
            }
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__("We can't save Attribute Set Mapping."));
        }
        $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
    }

    /**
     * Delete attribute set mapping
     */
    public function deleteAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('Magento_GoogleShopping_Model_Type');
            $model->load($id);
            if ($model->getTypeId()) {
                $model->delete();
            }
            $this->_getSession()->addSuccess(__('Attribute set mapping was deleted'));
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_getSession()->addError(__("We can't delete Attribute Set Mapping."));
        }
        $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
    }

    /**
     * Get Google Content attributes list
     */
    public function loadAttributesAction()
    {
        try {
            $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Attributes')
                ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'))
                ->setTargetCountry($this->getRequest()->getParam('target_country'))
                ->setAttributeSetSelected(true)
                ->toHtml()
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            // just need to output text with error
            $this->_getSession()->addError(__("We can't load attributes."));
        }
    }

    /**
     * Get available attribute sets
     */
    protected function loadAttributeSetsAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->getBlockSingleton('Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Form')
                    ->getAttributeSetsSelectElement($this->getRequest()->getParam('target_country'))
                    ->toHtml()
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            // just need to output text with error
            $this->_getSession()->addError(__("We can't load attribute sets."));
        }
    }

    /**
     * Get store object, basing on request
     *
     * @return Magento_Core_Model_Store
     */
    public function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if ($storeId == 0) {
            return Mage::app()->getDefaultStoreView();
        }
        return Mage::app()->getStore($storeId);
    }

    /**
     * Check access to this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GoogleShopping::types');
    }
}

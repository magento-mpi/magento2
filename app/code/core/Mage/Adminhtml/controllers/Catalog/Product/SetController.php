<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml entity sets controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Catalog_Product_SetController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attribute Sets'));

        $this->_setTypeId();

        $this->loadLayout();
        $this->_setActiveMenu('catalog/attributes/sets');

        $this->_addBreadcrumb(Mage::helper('Mage_Catalog_Helper_Data')->__('Catalog'), Mage::helper('Mage_Catalog_Helper_Data')->__('Catalog'));
        $this->_addBreadcrumb(
            Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Attribute Sets'),
            Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Attribute Sets'));

        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main')
        );
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Grid')
        );

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attribute Sets'));

        $this->_setTypeId();
        $attributeSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_title($attributeSet->getId() ? $attributeSet->getAttributeSetName() : $this->__('New Set'));

        Mage::register('current_attribute_set', $attributeSet);

        $this->loadLayout();
        $this->_setActiveMenu('catalog/attributes/sets');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper('Mage_Catalog_Helper_Data')->__('Catalog'), Mage::helper('Mage_Catalog_Helper_Data')->__('Catalog'));
        $this->_addBreadcrumb(
            Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Product Sets'),
            Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Product Sets'));

        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main'));

        $this->renderLayout();
    }

    public function setGridAction()
    {
        $this->_setTypeId();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Grid')
                ->toHtml());
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     */
    public function saveAction()
    {
        $entityTypeId   = $this->_getEntityTypeId();
        $hasError       = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        $isNewSet       = $this->getRequest()->getParam('gotoEdit', false) == '1';

        /* @var $model Mage_Eav_Model_Entity_Attribute_Set */
        $model  = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
            ->setEntityTypeId($entityTypeId);

        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');

        try {
            if ($isNewSet) {
                //filter html tags
                $name = $helper->stripTags($this->getRequest()->getParam('attribute_set_name'));
                $model->setAttributeSetName(trim($name));
            } else {
                if ($attributeSetId) {
                    $model->load($attributeSetId);
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('Mage_Catalog_Helper_Data')->__('This attribute set no longer exists.'));
                }
                $data = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($this->getRequest()->getPost('data'));

                //filter html tags
                $data['attribute_set_name'] = $helper->stripTags($data['attribute_set_name']);

                $model->organizeData($data);
            }

            $model->validate();
            if ($isNewSet) {
                $model->save();
                $model->initFromSkeleton($this->getRequest()->getParam('skeleton_set'));
            }
            $model->save();
            $this->_getSession()->addSuccess(Mage::helper('Mage_Catalog_Helper_Data')->__('The attribute set has been saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e,
                Mage::helper('Mage_Catalog_Helper_Data')->__('An error occurred while saving the attribute set.'));
            $hasError = true;
        }

        if ($isNewSet) {
            if ($hasError) {
                $this->_redirect('*/*/add');
            } else {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            }
        } else {
            $response = array();
            if ($hasError) {
                $this->_initLayoutMessages('Mage_Adminhtml_Model_Session');
                $response['error']   = 1;
                $response['message'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
            } else {
                $response['error']   = 0;
                $response['url']     = $this->getUrl('*/*/');
            }
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($response));
        }
    }

    public function addAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attribute Sets'))
             ->_title($this->__('New Set'));

        $this->_setTypeId();

        $this->loadLayout();
        $this->_setActiveMenu('catalog/attributes/sets');

        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add')
        );

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $setId = $this->getRequest()->getParam('id');
        try {
            Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->setId($setId)
                ->delete();

            $this->_getSession()->addSuccess($this->__('The attribute set has been removed.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred while deleting this set.'));
            $this->_redirectReferer();
        }
    }

    /**
     * Define in register catalog_product entity type code as entityType
     *
     */
    protected function _setTypeId()
    {
        Mage::register('entityType',
            Mage::getModel('Mage_Catalog_Model_Product')->getResource()->getTypeId());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('catalog/attributes/sets');
    }

    /**
     * Retrieve catalog product entity type id
     *
     * @return int
     */
    protected function _getEntityTypeId()
    {
        if (is_null(Mage::registry('entityType'))) {
            $this->_setTypeId();
        }
        return Mage::registry('entityType');
    }
}

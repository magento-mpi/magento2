<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Controller for Customer Attributes Management
 */
class Enterprise_Customer_Adminhtml_Customer_AttributeController extends Mage_Adminhtml_Controller_Action
{
    protected $_entityTypeId;

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')
            ->setType('customer')->getTypeId();
    }

    /**
     * Return adminhtml session object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Enterprise_Customer_Adminhtml_Customer_AttributeController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/attributes')
            ->_addBreadcrumb(Mage::helper('enterprise_customer')->__('Customer'), Mage::helper('enterprise_customer')->__('Customer'))
            ->_addBreadcrumb(Mage::helper('enterprise_customer')->__('Manage Customer Attributes'), Mage::helper('enterprise_customer')->__('Manage Customer Attributes'));
        return $this;
    }

    /**
     * Attributes grid
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('enterprise_customer/adminhtml_customer_attribute'))
            ->renderLayout();
    }

    /**
     * Create new attribute action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit attribute action
     *
     */
    public function editAction()
    {
        /* @var $attributeObject Mage_Customer_Model_Attribute */
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = Mage::getModel('customer/attribute')
            ->setEntityTypeId($this->_entityTypeId);

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()
                    ->addError(Mage::helper('enterprise_customer')->__('Attribute is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_entityTypeId) {
                $this->_getSession()->addError(Mage::helper('enterprise_customer')->__('You cannot edit this attribute.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        Mage::register('entity_attribute', $attributeObject);
        $this->_initAction()
            ->_addBreadcrumb(
                $attributeId ? Mage::helper('enterprise_customer')->__('Edit Customer Attribute') : Mage::helper('enterprise_customer')->__('New Customer Attribute'),
                $attributeId ? Mage::helper('enterprise_customer')->__('Edit Customer Attribute') : Mage::helper('enterprise_customer')->__('New Customer Attribute'))
            ->_addContent(
                $this->getLayout()->createBlock('enterprise_customer/adminhtml_customer_attribute_edit')
                    ->setData('action', $this->getUrl('*/customer_attribute/save'))
            )
            ->_addLeft($this->getLayout()->createBlock('enterprise_customer/adminhtml_customer_attribute_edit_tabs'))
            ->_addJs(
                $this->getLayout()->createBlock('adminhtml/template')
                    ->setTemplate('enterprise/customer/attribute/js.phtml')
            )
            ->renderLayout();
    }

    /**
     * Validate attribute action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $attributeObject = Mage::getModel('customer/attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);
        if ($attributeObject->getId() && !$attributeId) {
            $this->_getSession()->addError(Mage::helper('enterprise_customer')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save attribute action
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            /* @var $attributeObject Mage_Customer_Model_Attribute */
            $attributeObject = Mage::getModel('customer/attribute');

            if ($attributeId = $this->getRequest()->getParam('attribute_id')) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_entityTypeId) {
                    $this->_getSession()->addError(Mage::helper('enterprise_customer')->__('You cannot edit this attribute.'));
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code'] = $attributeObject->getAttributeCode();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['frontend_input'] = $attributeObject->getFrontendInput();
            }

            if (is_null($attributeObject->getIsUserDefined()) || $attributeObject->getIsUserDefined() != 0) {
                $data['backend_type'] = $attributeObject->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $attributeObject->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (isset($data['frontend_input']) && $data['frontend_input'] == 'multiselect') {
                $data['backend_model'] = 'eav/entity_attribute_backend_array';
            }
            $usedIn = array();
            if (isset($data['used_in'])) {
                $usedIn = $data['used_in'];
                unset($data['used_in']);
            }

            $attributeObject->addData($data);

            if (!$attributeId) {
                $attributeObject->setEntityTypeId($this->_entityTypeId)
                    ->setIsUserDefined(1);
            }

            try {
                $attributeObject->save();
                /* @var $formElements Mage_Eav_Model_Mysql4_Form_Element_Collection */
                $formElements = Mage::getResourceModel('eav/form_element_collection')
                    ->addAttributeFilter($attributeObject);
                foreach ($formElements as $element) {
                    if (!in_array($element->getTypeId(), $usedIn)) {
                        $element->delete();
                    }
                }
                foreach ($usedIn as $formId) {
                    if (!$formElements->getItemByColumnValue('type_id', $formId)) {
                        $_formElement = Mage::getModel('eav/form_element')->setData(array(
                            'type_id' => $formId,
                            'fieldset_id' => null,
                            'attribute_id' => $attributeObject->getId(),
                            'sort_order' => 0
                        ));
                        $formElements->addItem($_formElement);
                    }
                }
                $formElements->save();
                $this->_getSession()->addSuccess(Mage::helper('enterprise_customer')->__('Customer attribute was successfully saved.'));
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $attributeObject->getId(), '_current' => true));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Delete attribute action
     *
     */
    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = Mage::getModel('customer/attribute')->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_entityTypeId) {
                $this->_getSession()->addError(Mage::helper('enterprise_customer')->__('You cannot delete this attribute.'));
                $this->_redirect('*/*/');
                return;
            }
            try {
                $attributeObject->delete();
                $this->_getSession()->addSuccess(Mage::helper('enterprise_customer')->__('Customer attribute was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check whether attributes management functionality is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer/attributes/customer_attributes');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage Customer Address Attributes Controller
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Controller_Adminhtml_Customer_Address_Attribute
    extends Magento_Adminhtml_Controller_Action
{
    /**
     * Customer Address Entity Type instance
     *
     * @var Magento_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Return Customer Address Entity Type instance
     *
     * @return Magento_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('customer_address');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Enterprise_Customer_Controller_Adminhtml_Customer_Address_Attribute
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_Customer::customer_attributes_customer_address_attributes')
            ->_addBreadcrumb(
                Mage::helper('Enterprise_Customer_Helper_Data')->__('Customer'),
                Mage::helper('Enterprise_Customer_Helper_Data')->__('Customer'))
            ->_addBreadcrumb(
                Mage::helper('Enterprise_Customer_Helper_Data')->__('Manage Customer Address Attributes'),
                Mage::helper('Enterprise_Customer_Helper_Data')->__('Manage Customer Address Attributes'));
        return $this;
    }

    /**
     * Retrieve customer attribute object
     *
     * @return Magento_Customer_Model_Attribute
     */
    protected function _initAttribute()
    {
        $attribute = Mage::getModel('Magento_Customer_Model_Attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Attributes grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Customer Address Attributes'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new attribute action
     *
     */
    public function newAction()
    {
        $this->addActionLayoutHandles();
        $this->_forward('edit');
    }

    /**
     * Edit attribute action
     *
     */
    public function editAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        /* @var $attributeObject Magento_Customer_Model_Attribute */
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title($this->__('Customer Address Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('Attribute is no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('You cannot edit this attribute.')
                );
                $this->_redirect('*/*/');
                return;
            }

            $this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title($this->__('New Customer Address Attribute'));
        }

        // restore attribute data
        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }

        // register attribute object
        Mage::register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId()
            ? Mage::helper('Enterprise_Customer_Helper_Data')->__('Edit Customer Address Attribute')
            : Mage::helper('Enterprise_Customer_Helper_Data')->__('New Customer Address Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label)
            ->renderLayout();
    }

    /**
     * Validate attribute action
     *
     */
    public function validateAction()
    {
        $response = new Magento_Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('An attribute with this code already exists.')
                );

                $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        return Mage::helper('Enterprise_Customer_Helper_Address')->filterPostData($data);
    }

    /**
     * Save attribute action
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject Magento_Customer_Model_Attribute */
            $attributeObject = $this->_initAttribute();
            /* @var $helper Enterprise_Customer_Helper_Data */
            $helper = Mage::helper('Enterprise_Customer_Helper_Data');

            //filtering
            try {
                $data = $this->_filterPostData($data);
            } catch (Magento_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                    if (isset($data['attribute_id'])) {
                        $this->_redirect('*/*/edit', array('_current' => true));
                    } else {
                        $this->_redirect('*/*/new', array('_current' => true));
                    }
                    return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('Enterprise_Customer_Helper_Data')->__('You cannot edit this attribute.')
                    );
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code']     = $attributeObject->getAttributeCode();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['frontend_input']     = $attributeObject->getFrontendInput();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['is_system']          = $attributeObject->getIsSystem();
            } else {
                $data['backend_model']      = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model']       = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type']       = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined']    = 1;
                $data['is_system']          = 0;

                // add set and group info
                $data['attribute_set_id']   = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set')
                    ->getDefaultGroupId($data['attribute_set_id']);
            }

            if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'adminhtml_customer_address';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                $data[$scopeKeyPrefix . 'default_value'] = $helper->stripTags(
                    $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField));
            }

            $data['entity_type_id']     = $this->_getEntityType()->getId();
            $data['validate_rules']     = $helper->getAttributeValidateRules($data['frontend_input'], $data);

            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }

            try {
                $attributeObject->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('You saved the customer address attribute.')
                );
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'attribute_id'  => $attributeObject->getId(),
                        '_current'      => true
                    ));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('Something went wrong saving the customer address attribute.')
                );
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
            $attributeObject = $this->_initAttribute()->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || !$attributeObject->getIsUserDefined())
            {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('You cannot delete this attribute.')
                );
                $this->_redirect('*/*/');
                return;
            }
            try {
                $attributeObject->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('You deleted the customer address attribute.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('Enterprise_Customer_Helper_Data')->__('Something went wrong deleting the customer address attribute.')
                );
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
        return $this->_authorization->isAllowed('Enterprise_Customer::customer_address_attributes');
    }
}

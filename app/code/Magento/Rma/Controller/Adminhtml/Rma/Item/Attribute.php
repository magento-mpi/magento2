<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Controller\Adminhtml\Rma\Item;

class Attribute extends \Magento\Adminhtml\Controller\Action
{
    /**
     * RMA Item Entity Type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * Return RMA Item Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = \Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType('rma_item');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Rma::sales_magento_rma_rma_item_attribute')
            ->_addBreadcrumb(
                __('RMA'),
                __('RMA'))
            ->_addBreadcrumb(
                __('Manage RMA Item Attributes'),
                __('Manage RMA Item Attributes'));
        return $this;
    }

    /**
     * Retrieve RMA item attribute object
     *
     * @return \Magento\Rma\Model\Item\Attribute
     */
    protected function _initAttribute()
    {
        $attribute = \Mage::getModel('Magento\Rma\Model\Item\Attribute');
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
        $this->_title(__('Returns Attributes'));
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
        /* @var $attributeObject \Magento\Rma\Model\Item\Attribute */
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title(__('Returns Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()
                    ->addError(__('Attribute is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->_getSession()->addError(__('You cannot edit this attribute.'));
                $this->_redirect('*/*/');
                return;
            }

            $this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title(__('New Return Attribute'));
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        $attributeObject->setCanManageOptionLabels(true);
        \Mage::register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId()
            ? __('Edit Return Item Attribute')
            : __('New Return Item Attribute');

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
        $response = new \Magento\Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $attributeObject = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode)
                ->setCanManageOptionLabels(true);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    __('An attribute with the same code already exists.')
                );

                $this->_initLayoutMessages('Magento\Adminhtml\Model\Session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save attribute action
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject \Magento\Rma\Model\Item\Attribute */
            $attributeObject = $this->_initAttribute();
            /* @var $helper \Magento\Rma\Helper\Eav */
            $helper = \Mage::helper('Magento\Rma\Helper\Eav');

            try {
                $data = \Mage::helper('Magento\Rma\Helper\Eav')->filterPostData($data);
            } catch (\Magento\Core\Exception $e) {
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
                        __('You cannot edit this attribute.')
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
                $data['attribute_group_id'] = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
                    ->getDefaultGroupId($data['attribute_set_id']);
            }

            if (!isset($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'default';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                $data[$scopeKeyPrefix . 'default_value'] = $this->getRequest()
                    ->getParam($scopeKeyPrefix . $defaultValueField);
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

            $attributeObject->setCanManageOptionLabels(true);

            try {
                $this->_eventManager->dispatch('magento_rma_item_attribute_before_save', array(
                    'attribute' => $attributeObject
                ));

                $attributeObject->save();

                $this->_getSession()->addSuccess(
                    __('You saved the RMA item attribute.')
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
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            } catch (\Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong saving the RMA item attribute.')
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
            $attributeObject = $this->_initAttribute()->load($attributeId)
                ->setCanManageOptionLabels(true);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || !$attributeObject->getIsUserDefined()
            ) {
                $this->_getSession()->addError(
                    __('You cannot delete this attribute.')
                );
                $this->_redirect('*/*/');
                return;
            }
            try {
                $attributeObject->delete();

                $this->_getSession()->addSuccess(
                    __('You deleted the RMA attribute.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong deleting the RMA item attribute.')
                );
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }

        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check the permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rma::rma_attribute');
    }
}

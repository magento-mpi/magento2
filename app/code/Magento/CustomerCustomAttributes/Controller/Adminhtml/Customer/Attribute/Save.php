<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute;

class Save extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute
{
    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        return $this->_objectManager->get('Magento\CustomerCustomAttributes\Helper\Customer')->filterPostData($data);
    }

    /**
     * Save attribute action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject \Magento\Customer\Model\Attribute */
            $attributeObject = $this->_initAttribute();
            /* @var $helper \Magento\CustomerCustomAttributes\Helper\Data */
            $helper = $this->_objectManager->get('Magento\CustomerCustomAttributes\Helper\Data');
            /* @var $filterManager \Magento\Framework\Filter\FilterManager */
            $filterManager = $this->_objectManager->get('Magento\Framework\Filter\FilterManager');

            //filtering
            try {
                $data = $this->_filterPostData($data);
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                if (isset($data['attribute_id'])) {
                    $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                } else {
                    $this->_redirect('adminhtml/*/new', ['_current' => true]);
                }
                return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->messageManager->addError(__('You cannot edit this attribute.'));
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('adminhtml/*/');
                    return;
                }

                $data['attribute_code'] = $attributeObject->getAttributeCode();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['frontend_input'] = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['is_system'] = $attributeObject->getIsSystem();
            } else {
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type'] = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined'] = 1;
                $data['is_system'] = 0;

                // add set and group info
                $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
                /** @var $attrSet \Magento\Eav\Model\Entity\Attribute\Set */
                $attrSet = $this->_attrSetFactory->create();
                $data['attribute_group_id'] = $attrSet->getDefaultGroupId($data['attribute_set_id']);
            }

            if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'adminhtml_customer';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = $this->getRequest()->getParam('website') ? 'scope_' : '';
                $data[$scopeKeyPrefix . 'default_value'] = $filterManager->stripTags(
                    $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField)
                );
            }

            $data['entity_type_id'] = $this->_getEntityType()->getId();
            $data['validate_rules'] = $helper->getAttributeValidateRules($data['frontend_input'], $data);

            $validateRulesErrors = $helper->checkValidateRules($data['frontend_input'], $data['validate_rules']);
            if (count($validateRulesErrors)) {
                foreach ($validateRulesErrors as $message) {
                    $this->messageManager->addError($message);
                }
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }

            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            $useDefaults = $this->getRequest()->getPost('use_default');
            if ($useDefaults) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }

            try {
                $this->_eventManager->dispatch(
                    'magento_customercustomattributes_attribute_before_save',
                    ['attribute' => $attributeObject]
                );
                $attributeObject->save();
                $this->_eventManager->dispatch(
                    'magento_customercustomattributes_attribute_save',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccess(__('You saved the customer attribute.'));
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect(
                        'adminhtml/*/edit',
                        ['attribute_id' => $attributeObject->getId(), '_current' => true]
                    );
                } else {
                    $this->_redirect('adminhtml/*/');
                }
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong saving the customer attribute.'));
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
        return;
    }
}

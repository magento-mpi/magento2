<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Attribute;

use Magento\Catalog\Model\Product\AttributeSet\AlreadyExistsException;

class Save extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var $session \Magento\Backend\Model\Auth\Session */
            $session = $this->_objectManager->get('Magento\Backend\Model\Session');
            $setId = $this->getRequest()->getParam('set');

            $attributeSet = null;
            if (!empty($data['new_attribute_set_name'])) {
                $name = $this->_objectManager->get(
                    'Magento\Framework\Filter\FilterManager'
                )->stripTags(
                    $data['new_attribute_set_name']
                );
                $name = trim($name);

                try {
                    /** @var $attributeSet \Magento\Eav\Model\Entity\Attribute\Set */
                    $attributeSet = $this->_objectManager->create('Magento\Catalog\Model\Product\AttributeSet\Build')
                        ->setEntityTypeId($this->_entityTypeId)
                        ->setSkeletonId($setId)
                        ->setName($name)
                        ->getAttributeSet();
                } catch (AlreadyExistsException $alreadyExists) {
                    $this->messageManager->addError(__('Attribute Set with name \'%1\' already exists.', $name));
                    $this->messageManager->setAttributeData($data);
                    $this->_redirect('catalog/*/edit', array('_current' => true));
                    return;
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong saving the attribute.'));
                }
            }

            $redirectBack = $this->getRequest()->getParam('back', false);
            /* @var $model \Magento\Catalog\Model\Resource\Eav\Attribute */
            $model = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
            /* @var $helper \Magento\Catalog\Helper\Product */
            $helper = $this->_objectManager->get('Magento\Catalog\Helper\Product');

            $attributeId = $this->getRequest()->getParam('attribute_id');

            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $frontendLabel = $this->getRequest()->getParam('frontend_label');
            $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);
            if (strlen($attributeCode) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,30}$/'));
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addError(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );
                    $this->_redirect('catalog/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                    return;
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $inputType \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator */
                $inputType = $this->_objectManager->create(
                    'Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator'
                );
                if (!$inputType->isValid($data['frontend_input'])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addError($message);
                    }
                    $this->_redirect('catalog/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                    return;
                }
            }

            if ($attributeId) {
                $model->load($attributeId);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This attribute no longer exists.'));
                    $this->_redirect('catalog/*/');
                    return;
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $this->messageManager->addError(__('You can\'t update your attribute.'));
                    $session->setAttributeData($data);
                    $this->_redirect('catalog/*/');
                    return;
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }

            $data += array('is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => array());

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!$model->getIsUserDefined() && $model->getId()) {
                // Unset attribute field for system attributes
                unset($data['apply_to']);
            }

            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

            $groupCode = $this->getRequest()->getParam('group');
            if ($setId && $groupCode) {
                // For creating product attribute on product page we need specify attribute set and group
                $attributeSetId = !is_null($attributeSet) ? $attributeSet->getId() : $setId;
                $groupCollection = !is_null($attributeSet)
                    ? $attributeSet->getGroups()
                    : $this->_objectManager->create(
                        'Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection'
                    )->setAttributeSetFilter(
                        $attributeSetId
                    )->load();
                foreach ($groupCollection as $group) {
                    if ($group->getAttributeGroupCode() == $groupCode) {
                        $attributeGroupId = $group->getAttributeGroupId();
                        break;
                    }
                }
                $model->setAttributeSetId($attributeSetId);
                $model->setAttributeGroupId($attributeGroupId);
            }

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the product attribute.'));

                $this->_attributeLabelCache->clean();
                $session->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $requestParams = array(
                        'attributeId' => $this->getRequest()->getParam('product'),
                        'attribute' => $model->getId(),
                        '_current' => true,
                        'product_tab' => $this->getRequest()->getParam('product_tab')
                    );
                    if (!is_null($attributeSet)) {
                        $requestParams['new_attribute_set_id'] = $attributeSet->getId();
                    }
                    $this->_redirect('catalog/product/addAttribute', $requestParams);
                } elseif ($redirectBack) {
                    $this->_redirect('catalog/*/edit', array('attribute_id' => $model->getId(), '_current' => true));
                } else {
                    $this->_redirect('catalog/*/', array());
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('catalog/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }
        $this->_redirect('catalog/*/');
    }
}

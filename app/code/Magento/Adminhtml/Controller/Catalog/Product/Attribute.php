<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product attribute controller
 */
class Magento_Adminhtml_Controller_Catalog_Product_Attribute extends Magento_Adminhtml_Controller_Action
{
    /**
     * @var Magento_Cache_FrontendInterface
     */
    private $_attributeLabelCache;

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Cache_FrontendInterface $attributeLabelCache
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Cache_FrontendInterface $attributeLabelCache,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_attributeLabelCache = $attributeLabelCache;
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('Magento_Eav_Model_Entity')->setType(Magento_Catalog_Model_Product::ENTITY)
            ->getTypeId();
    }

    protected function _initAction()
    {
        $this->_title(__('Product Attributes'));

        if($this->getRequest()->getParam('popup')) {
            $this->loadLayout(array('popup', $this->getDefaultLayoutHandle() . '_popup'));
            $this->getLayout()->getBlock('root')->addBodyClass('attribute-popup');
        } else {
            $this->loadLayout()
                ->_addBreadcrumb(
                    __('Catalog'),
                    __('Catalog')
                )
                ->_addBreadcrumb(
                    __('Manage Product Attributes'),
                    __('Manage Product Attributes')
                );
                $this->_setActiveMenu('Magento_Catalog::catalog_attributes_attributes');
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Attribute'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        /** @var $model Magento_Catalog_Model_Resource_Eav_Attribute */
        $model = Mage::getModel('Magento_Catalog_Model_Resource_Eav_Attribute')
            ->setEntityTypeId($this->_entityTypeId);
        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('This attribute no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('This attribute cannot be edited.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getAttributeData(true);
        if (! empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->_coreRegistry->register('entity_attribute', $model);

        $this->_initAction();

        $this->_title($id ? $model->getName() : __('New Product Attribute'));

        $item = $id ? __('Edit Product Attribute')
                    : __('New Product Attribute');

        $this->_addBreadcrumb($item, $item);

        $this->getLayout()->getBlock('attribute_edit_js')
            ->setIsPopup((bool)$this->getRequest()->getParam('popup'));

        $this->renderLayout();

    }

    public function validateAction()
    {
        $response = new Magento_Object();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('Magento_Catalog_Model_Resource_Eav_Attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            if (strlen($this->getRequest()->getParam('attribute_code'))) {
                $response->setAttributes(
                    array(
                        'attribute_code' => __('An attribute with this code already exists.')
                    )
                );
            } else {
                $response->setAttributes(
                    array(
                        'attribute_label' => __('Attribute with the same code (%1) already exists.', $attributeCode)
                    )
                );
            }
            $response->setError(true);
        }
        if ($this->getRequest()->has('new_attribute_set_name')) {
            $setName = $this->getRequest()->getParam('new_attribute_set_name');
            /** @var $attributeSet Magento_Eav_Model_Entity_Attribute_Set */
            $attributeSet = $this->_objectManager->create('Magento_Eav_Model_Entity_Attribute_Set');
            $attributeSet->setEntityTypeId($this->_entityTypeId)->load($setName, 'attribute_set_name');
            if ($attributeSet->getId()) {
                $setName = $this->_objectManager->get('Magento_Core_Helper_Data')->escapeHtml($setName);
                $this->_getSession()->addError(
                    __('Attribute Set with name \'%1\' already exists.', $setName)
                );

                $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    private function generateCode($label)
    {
        $code = substr(preg_replace(
            '/[^a-z_0-9]/',
            '_',
            $this->_objectManager->create('Magento_Catalog_Model_Product_Url')->formatUrlKey($label)
        ), 0, 30);
        $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/'));
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var $session Magento_Backend_Model_Auth_Session */
            $session = Mage::getSingleton('Magento_Adminhtml_Model_Session');

            $isNewAttributeSet = false;
            if (!empty($data['new_attribute_set_name'])) {
                /** @var $attributeSet Magento_Eav_Model_Entity_Attribute_Set */
                $attributeSet = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set');
                $name = Mage::helper('Magento_Adminhtml_Helper_Data')->stripTags($data['new_attribute_set_name']);
                $name = trim($name);
                $attributeSet->setEntityTypeId($this->_entityTypeId)
                    ->load($name, 'attribute_set_name');

                if ($attributeSet->getId()) {
                    $session->addError(
                        __('Attribute Set with name \'%1\' already exists.', $name)
                    );
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/edit', array('_current' => true));
                    return;
                }

                try {
                    $attributeSet->setAttributeSetName($name)->validate();
                    $attributeSet->save();
                    $attributeSet->initFromSkeleton($this->getRequest()->getParam('set'))->save();
                    $isNewAttributeSet = true;
                } catch (Magento_Core_Exception $e) {
                    $session->addError($e->getMessage());
                } catch (Exception $e) {
                    $session->addException($e, __('Something went wrong saving the attribute.'));
                }
            }

            $redirectBack   = $this->getRequest()->getParam('back', false);
            /* @var $model Magento_Catalog_Model_Resource_Eav_Attribute */
            $model = Mage::getModel('Magento_Catalog_Model_Resource_Eav_Attribute');
            /* @var $helper Magento_Catalog_Helper_Product */
            $helper = Mage::helper('Magento_Catalog_Helper_Product');

            $id = $this->getRequest()->getParam('attribute_id');

            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $frontendLabel = $this->getRequest()->getParam('frontend_label');
            $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);
            if (strlen($this->getRequest()->getParam('attribute_code')) > 0) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,30}$/'));
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $session->addError(
                        __('Attribute code "%1" is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.', $attributeCode)
                    );
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $validatorInput Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInput = Mage::getModel('Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator');
                if (!$validatorInput->isValid($data['frontend_input'])) {
                    foreach ($validatorInput->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $session->addError(
                        __('This attribute no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        __('You can\'t update your attribute.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
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

            $data += array(
                'is_configurable' => 0,
                'is_filterable' => 0,
                'is_filterable_in_search' => 0,
                'apply_to' => array(),
            );

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!$model->getIsUserDefined() && $model->getId()) {
                unset($data['apply_to']); //Unset attribute field for system attributes
            }

            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

            $groupCode = $this->getRequest()->getParam('group');
            if ($this->getRequest()->getParam('set') && $groupCode) {
                // For creating product attribute on product page we need specify attribute set and group
                $attributeSetId = $isNewAttributeSet ? $attributeSet->getId() : $this->getRequest()->getParam('set');
                $groupCollection = $isNewAttributeSet
                    ? $attributeSet->getGroups()
                    : Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Group_Collection')
                        ->setAttributeSetFilter($attributeSetId)
                        ->load();
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
                $session->addSuccess(__('You saved the product attribute.'));

                $this->_attributeLabelCache->clean();
                $session->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $requestParams = array(
                        'id'       => $this->getRequest()->getParam('product'),
                        'attribute'=> $model->getId(),
                        '_current' => true,
                        'product_tab' => $this->getRequest()->getParam('product_tab'),
                    );
                    if ($isNewAttributeSet) {
                        $requestParams['new_attribute_set_id'] = $attributeSet->getId();
                    }
                    $this->_redirect('adminhtml/catalog_product/addAttribute', $requestParams);
                } elseif ($redirectBack) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current'=>true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        if ($id) {
            $model = Mage::getModel('Magento_Catalog_Model_Resource_Eav_Attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('The product attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
            __('We can\'t find an attribute to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Search for attributes by part of attribute's label in admin store
     */
    public function suggestConfigurableAttributesAction()
    {
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode(
            $this->getLayout()->createBlock('Magento_Catalog_Block_Product_Configurable_AttributeSelector')
                ->getSuggestedAttributes($this->getRequest()->getParam('label_part'))
        ));
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::attributes_attributes');
    }
}

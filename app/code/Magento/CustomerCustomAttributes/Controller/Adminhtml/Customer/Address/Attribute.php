<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address;

/**
 * Manage Customer Address Attributes Controller
 */
class Attribute
    extends \Magento\Backend\App\Action
{
    /**
     * Customer Address Entity Type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_attrSetFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\AttributeFactory $attrFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\AttributeFactory $attrFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eavConfig = $eavConfig;
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        parent::__construct($context);
    }

    /**
     * Return Customer Address Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = $this->_eavConfig->getEntityType('customer_address');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerCustomAttributes::customer_attributes_customer_address_attributes')
            ->_addBreadcrumb(
                __('Customer'),
                __('Customer'))
            ->_addBreadcrumb(
                __('Manage Customer Address Attributes'),
                __('Manage Customer Address Attributes'));
        return $this;
    }

    /**
     * Retrieve customer attribute object
     *
     * @return \Magento\Customer\Model\Attribute
     */
    protected function _initAttribute()
    {
        /** @var $attribute \Magento\Customer\Model\Attribute */
        $attribute = $this->_attrFactory->create();
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Attributes grid
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Customer Address Attributes'));
        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Create new attribute action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_view->addActionLayoutHandles();
        $this->_forward('edit');
    }

    /**
     * Edit attribute action
     *
     * @return void
     */
    public function editAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        /* @var $attributeObject \Magento\Customer\Model\Attribute */
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title->add(__('Customer Address Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->messageManager->addError(
                    __('Attribute is no longer exists.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->messageManager->addError(
                    __('You cannot edit this attribute.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            }

            $this->_title->add($attributeObject->getFrontendLabel());
        } else {
            $this->_title->add(__('New Customer Address Attribute'));
        }

        // restore attribute data
        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }

        // register attribute object
        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId()
            ? __('Edit Customer Address Attribute')
            : __('New Customer Address Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label);
        $this->_view->renderLayout();
    }

    /**
     * Validate attribute action
     *
     * @return void
     */
    public function validateAction()
    {
        $response = new \Magento\Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->messageManager->addError(
                    __('An attribute with this code already exists.')
                );

                $this->_view->getLayout()->initMessages();
                $response->setError(true);
                $response->setMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
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
        return $this->_objectManager->get('Magento\CustomerCustomAttributes\Helper\Address')->filterPostData($data);
    }

    /**
     * Save attribute action
     *
     * @return void
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject \Magento\Customer\Model\Attribute */
            $attributeObject = $this->_initAttribute();
            /* @var $helper \Magento\CustomerCustomAttributes\Helper\Data */
            $helper = $this->_objectManager->get('Magento\CustomerCustomAttributes\Helper\Data');
            /* @var $filterManager \Magento\Filter\FilterManager */
            $filterManager = $this->_objectManager->get('Magento\Filter\FilterManager');

            //filtering
            try {
                $data = $this->_filterPostData($data);
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                if (isset($data['attribute_id'])) {
                    $this->_redirect('adminhtml/*/edit', array('_current' => true));
                } else {
                    $this->_redirect('adminhtml/*/new', array('_current' => true));
                }
                return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->messageManager->addError(
                        __('You cannot edit this attribute.')
                    );
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('adminhtml/*/');
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
                /** @var $attrSet \Magento\Eav\Model\Entity\Attribute\Set */
                $attrSet = $this->_attrSetFactory->create();
                $data['attribute_group_id'] = $attrSet->getDefaultGroupId($data['attribute_set_id']);
            }

            if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'adminhtml_customer_address';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                $data[$scopeKeyPrefix . 'default_value'] = $filterManager->stripTags(
                    $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField)
                );
            }

            $data['entity_type_id']     = $this->_getEntityType()->getId();
            $data['validate_rules']     = $helper->getAttributeValidateRules($data['frontend_input'], $data);

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
                $attributeObject->save();
                $this->messageManager->addSuccess(
                    __('You saved the customer address attribute.')
                );
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('adminhtml/*/edit', array(
                        'attribute_id'  => $attributeObject->getId(),
                        '_current'      => true
                    ));
                } else {
                    $this->_redirect('adminhtml/*/');
                }
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('Something went wrong saving the customer address attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
        return;
    }

    /**
     * Delete attribute action
     *
     * @return void
     */
    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || !$attributeObject->getIsUserDefined())
            {
                $this->messageManager->addError(
                    __('You cannot delete this attribute.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            }
            try {
                $attributeObject->delete();
                $this->messageManager->addSuccess(
                    __('You deleted the customer address attribute.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('Something went wrong deleting the customer address attribute.')
                );
                $this->_redirect('adminhtml/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }

        $this->_redirect('adminhtml/*/');
        return;
    }

    /**
     * Check whether attributes management functionality is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CustomerCustomAttributes::customer_address_attributes');
    }
}

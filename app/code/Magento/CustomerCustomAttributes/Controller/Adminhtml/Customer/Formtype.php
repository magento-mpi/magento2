<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admihtml Manage Form Types Controller
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer;

class Formtype
    extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Eav\Model\Form\TypeFactory
     */
    protected $_formTypeFactory;

    /**
     * @var \Magento\Eav\Model\Form\FieldsetFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory
     */
    protected $_fieldsetsFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Form\Element\CollectionFactory
     */
    protected $_elementsFactory;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Eav\Model\Form\TypeFactory $formTypeFactory
     * @param \Magento\Eav\Model\Form\FieldsetFactory $fieldsetFactory
     * @param \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory $fieldsetsFactory
     * @param \Magento\Eav\Model\Resource\Form\Element\CollectionFactory $elementsFactory
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Eav\Model\Form\TypeFactory $formTypeFactory,
        \Magento\Eav\Model\Form\FieldsetFactory $fieldsetFactory,
        \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory $fieldsetsFactory,
        \Magento\Eav\Model\Resource\Form\Element\CollectionFactory $elementsFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_formTypeFactory = $formTypeFactory;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_fieldsetsFactory = $fieldsetsFactory;
        $this->_elementsFactory = $elementsFactory;
        parent::__construct($context);
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_CustomerCustomAttributes::customer_formtype')
            ->_addBreadcrumb(__('Customer'),
                __('Customer'))
            ->_addBreadcrumb(__('Manage Form Types'),
                __('Manage Form Types'));
        return $this;
    }

    /**
     * View form types grid
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Initialize and return current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _initFormType()
    {
        /** @var $model \Magento\Eav\Model\Form\Type */
        $model = $this->_formTypeFactory->create();
        $typeId = $this->getRequest()->getParam('type_id');
        if (is_numeric($typeId)) {
            $model->load($typeId);
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_form_type', $model);
        return $model;
    }

    /**
     * Create new form type by skeleton
     *
     */
    public function newAction()
    {
        $this->_coreRegistry->register('edit_mode', 'new');
        $this->_initFormType();
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new form type from skeleton
     *
     */
    public function createAction()
    {
        $skeleton = $this->_initFormType();
        $redirectUrl = $this->getUrl('*/*/*');
        if ($skeleton->getId()) {
            try {
                $hasError = false;
                /** @var $formType \Magento\Eav\Model\Form\Type */
                $formType = $this->_formTypeFactory->create();
                $formType->addData(array(
                    'code'          => $skeleton->getCode(),
                    'label'         => $this->getRequest()->getPost('label'),
                    'theme'         => $this->getRequest()->getPost('theme'),
                    'store_id'      => $this->getRequest()->getPost('store_id'),
                    'entity_types'  => $skeleton->getEntityTypes(),
                    'is_system'     => 0
                ));
                $formType->save();
                $formType->createFromSkeleton($skeleton);
            } catch(\Magento\Core\Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    __("We can't save the form type right now."));
            }
            if ($hasError) {
                $this->_getSession()->setFormData($this->getRequest()->getPost());
                $redirectUrl = $this->getUrl('*/*/new');
            } else {
                $redirectUrl = $this->getUrl('*/*/edit/', array('type_id' => $formType->getId()));
            }
        }

        $this->_redirectUrl($redirectUrl);
    }

    /**
     * Edit Form Type
     *
     */
    public function editAction()
    {
        $this->_coreRegistry->register('edit_mode', 'edit');
        $this->_initFormType();
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save Form Type Tree data
     *
     * @param \Magento\Eav\Model\Form\Type $formType
     * @param array $data
     */
    protected function _saveTreeData($formType, array $data)
    {
        /** @var $fieldsetCollection \Magento\Eav\Model\Resource\Form\Fieldset\Collection */
        $fieldsetCollection = $this->_fieldsetsFactory->create();
        $fieldsetCollection->addTypeFilter($formType)->setSortOrder();

        /** @var $elementCollection \Magento\Eav\Model\Resource\Form\Element\Collection */
        $elementCollection = $this->_elementsFactory->create();
        $elementCollection->addTypeFilter($formType)->setSortOrder();

        $fsUpdate   = array();
        $fsInsert   = array();
        $fsDelete   = array();
        $attributes = array();

        //parse tree data
        foreach ($data as $k => $v) {
            if (strpos($k, 'f_') === 0) {
                $fsInsert[] = $v;
            } else if (is_numeric($k)) {
                $fsUpdate[$k] = $v;
            } else if (strpos($k, 'a_') === 0) {
                $v['node_id'] = substr($v['node_id'], 2);
                $attributes[] = $v;
            }
        }

        foreach ($fieldsetCollection as $fieldset) {
            /* @var $fieldset \Magento\Eav\Model\Form\Fieldset */
            if (!isset($fsUpdate[$fieldset->getId()])) {
                // collect deleted fieldsets
                $fsDelete[$fieldset->getId()] = $fieldset;
            } else {
                // update fieldset
                $fsData = $fsUpdate[$fieldset->getId()];
                $fieldset->setCode($fsData['code'])
                    ->setLabels($fsData['labels'])
                    ->setSortOrder($fsData['sort_order'])
                    ->save();
            }
        }

        // insert new fieldsets
        $fsMap = array();
        foreach ($fsInsert as $fsData) {
            /** @var $fieldset \Magento\Eav\Model\Form\Fieldset */
            $fieldset = $this->_fieldsetFactory->create();
            $fieldset->setTypeId($formType->getId())
                ->setCode($fsData['code'])
                ->setLabels($fsData['labels'])
                ->setSortOrder($fsData['sort_order'])
                ->save();
            $fsMap[$fsData['node_id']] = $fieldset->getId();
        }

        // update attributes
        foreach ($attributes as $attrData) {
            $element = $elementCollection->getItemById($attrData['node_id']);
            if (!$element) {
                continue;
            }
            if (empty($attrData['parent'])) {
                $fieldsetId = null;
            } else if (is_numeric($attrData['parent'])) {
                $fieldsetId = (int)$attrData['parent'];
            } else if (strpos($attrData['parent'], 'f_') === 0) {
                $fieldsetId = $fsMap[$attrData['parent']];
            } else {
                continue;
            }

            $element->setFieldsetId($fieldsetId)
                ->setSortOrder($attrData['sort_order'])
                ->save();
        }

        // delete fieldsets
        foreach ($fsDelete as $fieldset) {
            $fieldset->delete();
        }
    }

    /**
     * Save form Type
     *
     */
    public function saveAction()
    {
        $formType = $this->_initFormType();
        $redirectUrl = $this->getUrl('*/*/index');
        if ($this->getRequest()->isPost() && $formType->getId()) {
            $request = $this->getRequest();
            try {
                $hasError = false;
                $formType->setLabel($request->getPost('label'));
                $formType->save();

                $treeData = $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->jsonDecode($request->getPost('form_type_data'));
                if (!empty($treeData) && is_array($treeData)) {
                    $this->_saveTreeData($formType, $treeData);
                }
            } catch (\Magento\Core\Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    __("We can't save the form type right now."));
            }

            if ($hasError) {
                $this->_getSession()->setFormData($this->getRequest()->getPost());
            }
            if ($hasError || $request->getPost('continue_edit')) {
                $redirectUrl = $this->getUrl('*/*/edit', array('type_id' => $formType->getId()));
            }
        }
        $this->_redirectUrl($redirectUrl);
    }

    /**
     * Delete form type
     *
     */
    public function deleteAction()
    {
        $formType = $this->_initFormType();
        if ($this->getRequest()->isPost() && $formType->getId()) {
            if ($formType->getIsSystem()) {
                $message = __('This system form type cannot be deleted.');
                $this->_getSession()->addError($message);
            } else {
                try {
                    $formType->delete();
                    $message = __('The form type has been deleted.');
                    $this->_getSession()->addSuccess($message);
                } catch (\Magento\Core\Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (\Exception $e) {
                    $message = __('Something went wrong deleting the form type.');
                    $this->_getSession()->addException($e, $message);
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(null);
    }
}

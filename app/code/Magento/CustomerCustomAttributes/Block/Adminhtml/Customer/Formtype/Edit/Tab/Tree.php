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
 * Form Type Edit General Tab Block
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Tab_Tree
    extends Magento_Backend_Block_Widget_Form
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Eav_Model_Resource_Form_Fieldset_CollectionFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var Magento_Eav_Model_Resource_Form_Element_CollectionFactory
     */
    protected $_elementsFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Eav_Model_Resource_Form_Fieldset_CollectionFactory $fieldsetFactory
     * @param Magento_Eav_Model_Resource_Form_Element_CollectionFactory $elementsFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Eav_Model_Resource_Form_Fieldset_CollectionFactory $fieldsetFactory,
        Magento_Eav_Model_Resource_Form_Element_CollectionFactory $elementsFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_elementsFactory = $elementsFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current form type instance
     *
     * @return Magento_Eav_Model_Form_Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    public function getTreeButtonsHtml()
    {
        $addButtonData = array(
            'id'        => 'add_node_button',
            'label'     => __('New Fieldset'),
            'onclick'   => 'formType.newFieldset()',
            'class'     => 'add',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData($addButtonData)->toHtml();
    }

    public function getFieldsetButtonsHtml()
    {
        $buttons = array();
        $buttons[] = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData(array(
            'id'        => 'save_node_button',
            'label'     => __('Save'),
            'onclick'   => 'formType.saveFieldset()',
            'class'     => 'save',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData(array(
            'id'        => 'delete_node_button',
            'label'     => __('Remove'),
            'onclick'   => 'formType.deleteFieldset()',
            'class'     => 'delete',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData(array(
            'id'        => 'cancel_node_button',
            'label'     => __('Cancel'),
            'onclick'   => 'formType.cancelFieldset()',
            'class'     => 'cancel',
        ))->toHtml();

        return join(' ', $buttons);
    }

    /**
     * Retrieve all store objects
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $this->setData('stores', $this->_storeManager->getStores(false));
        }
        return $this->_getData('stores');
    }

    /**
     * Retrieve stores array in JSON format
     *
     * @return string
     */
    public function getStoresJson()
    {
        $result = array();
        $stores = $this->getStores();
        foreach ($stores as $stores) {
            $result[$stores->getId()] = $stores->getName();
        }

        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Retrieve form attributes JSON
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $nodes = array();
        /** @var $fieldsetCollection Magento_Eav_Model_Resource_Form_Fieldset_Collection */
        $fieldsetCollection = $this->_fieldsetFactory->create();
        $fieldsetCollection->addTypeFilter($this->_getFormType())->setSortOrder();

        /** @var $elementCollection Magento_Eav_Model_Resource_Form_Element_Collection */
        $elementCollection  = $this->_elementsFactory->create();
        $elementCollection = $elementCollection->addTypeFilter($this->_getFormType())->setSortOrder();

        foreach ($fieldsetCollection as $fieldset) {
            /* @var $fieldset Magento_Eav_Model_Form_Fieldset */
            $node = array(
                'node_id'   => $fieldset->getId(),
                'parent'    => null,
                'type'      => 'fieldset',
                'code'      => $fieldset->getCode(),
                'label'     => $fieldset->getLabel()
            );

            foreach ($fieldset->getLabels() as $storeId => $label) {
                $node['label_' . $storeId] = $label;
            }

            $nodes[] = $node;
        }

        foreach ($elementCollection as $element) {
            /* @var $element Magento_Eav_Model_Form_Element */
            $nodes[] = array(
                'node_id'   => 'a_' . $element->getId(),
                'parent'    => $element->getFieldsetId(),
                'type'      => 'element',
                'code'      => $element->getAttribute()->getAttributeCode(),
                'label'     => $element->getAttribute()->getFrontend()->getLabel()
            );
        }

        return $this->_coreData->jsonEncode($nodes);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attributes');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Attributes');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}

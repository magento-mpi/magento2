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
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Tab;

class Tree
    extends \Magento\Backend\Block\Widget\Form
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Form\Element\CollectionFactory
     */
    protected $_elementsFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory $fieldsetFactory
     * @param \Magento\Eav\Model\Resource\Form\Element\CollectionFactory $elementsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Resource\Form\Fieldset\CollectionFactory $fieldsetFactory,
        \Magento\Eav\Model\Resource\Form\Element\CollectionFactory $elementsFactory,
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
     * @return \Magento\Eav\Model\Form\Type
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
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData($addButtonData)->toHtml();
    }

    public function getFieldsetButtonsHtml()
    {
        $buttons = array();
        $buttons[] = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData(array(
            'id'        => 'save_node_button',
            'label'     => __('Save'),
            'onclick'   => 'formType.saveFieldset()',
            'class'     => 'save',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData(array(
            'id'        => 'delete_node_button',
            'label'     => __('Remove'),
            'onclick'   => 'formType.deleteFieldset()',
            'class'     => 'delete',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData(array(
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
        /** @var $fieldsetCollection \Magento\Eav\Model\Resource\Form\Fieldset\Collection */
        $fieldsetCollection = $this->_fieldsetFactory->create();
        $fieldsetCollection->addTypeFilter($this->_getFormType())->setSortOrder();

        /** @var $elementCollection \Magento\Eav\Model\Resource\Form\Element\Collection */
        $elementCollection  = $this->_elementsFactory->create();
        $elementCollection = $elementCollection->addTypeFilter($this->_getFormType())->setSortOrder();

        foreach ($fieldsetCollection as $fieldset) {
            /* @var $fieldset \Magento\Eav\Model\Form\Fieldset */
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
            /* @var $element \Magento\Eav\Model\Form\Element */
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

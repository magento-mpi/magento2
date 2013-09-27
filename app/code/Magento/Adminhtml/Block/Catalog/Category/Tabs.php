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
 * Category tabs
 */
class Magento_Adminhtml_Block_Catalog_Category_Tabs extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'Magento_Adminhtml_Block_Catalog_Category_Tab_Attributes';

    protected $_template = 'widget/tabshoriz.phtml';

   /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Adminhtml catalog
     *
     * @var Magento_Adminhtml_Helper_Catalog
     */
    protected $_adminhtmlCatalog = null;

    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $collectionFactory
     * @param Magento_Adminhtml_Helper_Catalog $adminhtmlCatalog
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $collectionFactory,
        Magento_Adminhtml_Helper_Catalog $adminhtmlCatalog,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->_adminhtmlCatalog = $adminhtmlCatalog;
        parent::__construct($coreData, $context, $authSession, $data);
    }

    /**
     * Initialize Tabs
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(__('Category Data'));

    }

    /**
     * Retrieve cattegory object
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if ($block = $this->_adminhtmlCatalog->getCategoryAttributeTabBlock()) {
            return $block;
        }
        return $this->_attributeTabBlock;
    }

    /**
     * Prepare Layout Content
     *
     * @return Magento_Adminhtml_Block_Catalog_Category_Tabs
     */
    protected function _prepareLayout()
    {
        $categoryAttributes = $this->getCategory()->getAttributes();
        if (!$this->getCategory()->getId()) {
            foreach ($categoryAttributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getCategory()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }

        $attributeSetId     = $this->getCategory()->getDefaultAttributeSetId();
        /** @var $groupCollection Magento_Eav_Model_Resource_Entity_Attribute_Group_Collection */
        $groupCollection = $this->_collectionFactory->create()
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();
        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            /* @var $group Magento_Eav_Model_Entity_Attribute_Group */
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Magento_Eav_Model_Entity_Attribute_Group */
            $attributes = array();
            foreach ($categoryAttributes as $attribute) {
                /* @var $attribute Magento_Eav_Model_Entity_Attribute */
                if ($attribute->isInGroup($attributeSetId, $group->getId())) {
                    $attributes[] = $attribute;
                }
            }

            // do not add grops without attributes
            if (!$attributes) {
                continue;
            }

            $active  = $defaultGroupId == $group->getId();
            $block = $this->getLayout()->createBlock($this->getAttributeTabBlock(), $this->getNameInLayout() . '_tab_'
                . $group->getAttributeGroupName())
                ->setGroup($group)
                ->setAttributes($attributes)
                ->setAddHiddenFields($active)
                ->toHtml();
            $this->addTab('group_' . $group->getId(), array(
                'label'     => __($group->getAttributeGroupName()),
                'content'   => $block,
                'active'    => $active
            ));
        }

        $this->addTab('products', array(
            'label'     => __('Category Products'),
            'content'   => $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Catalog_Category_Tab_Product',
                'category.product.grid'
            )->toHtml(),
        ));

        // dispatch event add custom tabs
        $this->_eventManager->dispatch('adminhtml_catalog_category_tabs', array(
            'tabs'  => $this
        ));

        /*$this->addTab('features', array(
            'label'     => __('Feature Products'),
            'content'   => 'Feature Products'
        ));        */
        return parent::_prepareLayout();
    }
}

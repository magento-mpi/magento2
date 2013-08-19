<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes';

    protected $_template = 'widget/tabshoriz.phtml';

    /**
     * Initialize Tabs
     *
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
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return Adminhtml Catalog Helper
     *
     * @return Mage_Adminhtml_Helper_Catalog
     */
    public function getCatalogHelper()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Catalog');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if ($block = $this->getCatalogHelper()->getCategoryAttributeTabBlock()) {
            return $block;
        }
        return $this->_attributeTabBlock;
    }

    /**
     * Prepare Layout Content
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tabs
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
        /** @var $groupCollection Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection */
        $groupCollection    = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection')
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();
        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            /* @var $group Mage_Eav_Model_Entity_Attribute_Group */
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Mage_Eav_Model_Entity_Attribute_Group */
            $attributes = array();
            foreach ($categoryAttributes as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
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
                'Mage_Adminhtml_Block_Catalog_Category_Tab_Product',
                'category.product.grid'
            )->toHtml(),
        ));

        // dispatch event add custom tabs
        Mage::dispatchEvent('adminhtml_catalog_category_tabs', array(
            'tabs'  => $this
        ));

        /*$this->addTab('features', array(
            'label'     => __('Feature Products'),
            'content'   => 'Feature Products'
        ));        */
        return parent::_prepareLayout();
    }
}

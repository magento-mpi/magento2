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
 * Adminhtml Catalog Attribute Set Main Block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set;

class Main extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'catalog/product/attribute/set/main.phtml';

    /**
     * Prepare Global Layout
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main
     */
    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->addChild('group_tree', 'Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main\Tree\Group');

        $this->addChild('edit_set_form', 'Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main\Formset');

        $this->addChild('delete_group_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete Selected Group'),
            'onclick'   => 'editSet.submit();',
            'class'     => 'delete'
        ));

        $this->addChild('add_group_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Add New'),
            'onclick'   => 'editSet.addGroup();',
            'class'     => 'add'
        ));

        $this->addChild('back_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
            'class'     => 'back'
        ));

        $this->addChild('reset_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('save_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Save Attribute Set'),
            'onclick'   => 'editSet.save();',
            'class'     => 'save'
        ));

        $this->addChild('delete_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete Attribute Set'),
            'onclick'   => 'deleteConfirm(\''. $this->jsQuoteEscape(__('You are about to delete all products in this set. Are you sure you want to delete this attribute set?')) . '\', \'' . $this->getUrl('*/*/delete', array('id' => $setId)) . '\')',
            'class'     => 'delete'
        ));

        $this->addChild('rename_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('New Set Name'),
            'onclick'   => 'editSet.rename()'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Attribute Set Group Tree HTML
     *
     * @return string
     */
    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    /**
     * Retrieve Attribute Set Edit Form HTML
     *
     * @return string
     */
    public function getSetFormHtml()
    {
        return $this->getChildHtml('edit_set_form');
    }

    /**
     * Retrieve Block Header Text
     *
     * @return string
     */
    protected function _getHeader()
    {
        return __("Edit Attribute Set '%1'", $this->_getAttributeSet()->getAttributeSetName());
    }

    /**
     * Retrieve Attribute Set Save URL
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_product_set/save', array('id' => $this->_getSetId()));
    }

    /**
     * Retrieve Attribute Set Group Save URL
     *
     * @return string
     */
    public function getGroupUrl()
    {
        return $this->getUrl('*/catalog_product_group/save', array('id' => $this->_getSetId()));
    }

    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();

        /* @var $groups \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection */
        $groups = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();

        $configurable = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute')
            ->getUsedAttributes($setId);

        $unassignableAttributes = \Mage::helper('Magento\Catalog\Helper\Product')->getUnassignableAttributes();

        /* @var $node \Magento\Eav\Model\Entity\Attribute\Group */
        foreach ($groups as $node) {
            $item = array();
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;

            $nodeChildren = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = array();
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child \Magento\Eav\Model\Entity\Attribute */

                    $isUnassignable = !in_array($child->getAttributeCode(), $unassignableAttributes);

                    $attr = array(
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => $isUnassignable ? 'leaf' : 'system-leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => $child->getIsUserDefined(),
                        'is_configurable'   => (int)in_array($child->getAttributeId(), $configurable),
                        'is_unassignable'   => $isUnassignable,
                        'entity_id'         => $child->getEntityAttributeId()
                    );

                    $item['children'][] = $attr;
                }
            }

            $items[] = $item;
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($items);
    }

    /**
     * Retrieve Unused in Attribute Set Attribute Tree as JSON
     *
     * @return string
     */
    public function getAttributeTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();

        $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
            ->setAttributeSetFilter($setId)
            ->load();

        $attributesIds = array('0');
        /* @var $item \Magento\Eav\Model\Entity\Attribute */
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        $attributes = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
            ->setAttributesExcludeFilter($attributesIds)
            ->addVisibleFilter()
            ->load();

        foreach ($attributes as $child) {
            $attr = array(
                'text'              => $child->getAttributeCode(),
                'id'                => $child->getAttributeId(),
                'cls'               => 'leaf',
                'allowDrop'         => false,
                'allowDrag'         => true,
                'leaf'              => true,
                'is_user_defined'   => $child->getIsUserDefined(),
                'is_configurable'   => false,
                'entity_id'         => $child->getEntityId()
            );

            $items[] = $attr;
        }

        if (count($items) == 0) {
            $items[] = array(
                'text'      => __('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($items);
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Delete Group Button HTML
     *
     * @return string
     */
    public function getDeleteGroupButton()
    {
        return $this->getChildHtml('delete_group_button');
    }

    /**
     * Retrieve Add New Group Button HTML
     *
     * @return string
     */
    public function getAddGroupButton()
    {
        return $this->getChildHtml('add_group_button');
    }

    /**
     * Retrieve Rename Button HTML
     *
     * @return string
     */
    public function getRenameButton()
    {
        return $this->getChildHtml('rename_button');
    }

    /**
     * Retrieve current Attribute Set object
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected function _getAttributeSet()
    {
        return \Mage::registry('current_attribute_set');
    }

    /**
     * Retrieve current attribute set Id
     *
     * @return int
     */
    protected function _getSetId()
    {
        return $this->_getAttributeSet()->getId();
    }

    /**
     * Check Current Attribute Set is a default
     *
     * @return bool
     */
    public function getIsCurrentSetDefault()
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = \Mage::getModel('Magento\Eav\Model\Entity\Type')
                ->load(\Mage::registry('entityType'))
                ->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    /**
     * Prepare HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        \Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_main_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}

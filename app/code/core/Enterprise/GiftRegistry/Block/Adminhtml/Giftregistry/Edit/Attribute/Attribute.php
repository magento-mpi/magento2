<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute
    extends Enterprise_Enterprise_Block_Adminhtml_Widget_Form
{
    protected $_typeInstance;
    protected $_values;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/giftregistry/edit/attributes.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Attribute'),
                    'class' => 'add',
                    'id'    => 'add_new_attribute'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Attribute'),
                    'class' => 'delete delete-attribute-option'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    /**
     * Get gift registry attribute config model
     *
     * @return Enterprise_GiftRegistry_Model_Attribute_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('enterprise_giftregistry/attribute_config');
    }

    /**
     * Get gift registry attribute type
     */
    public function getType()
    {
        if (!$this->_typeInstance) {
            if ($type = Mage::registry('current_giftregistry_type')) {
                $this->_typeInstance = $type;
            } else {
                $this->_typeInstance = Mage::getSingleton('enterprise_giftregistry/type');
            }
        }
        return $this->_typeInstance;
    }

    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
            ->getBlock('registry.attributes')
            ->getChild('add_button')->getId();
        return $buttonId;
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Select element for choosing attribute type
     *
     * @return string
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id'    => 'attribute_{{id}}_type',
                'class' => 'select select-attribute-option-type required-option-select'
            ))
            ->setName('attribute[{{id}}][type]')
            ->setOptions($this->getConfig()->getAttributeTypesOptions());

        return $select->getHtml();
    }

    /**
     * Select element for choosing attribute group
     *
     * @return string
     */
    public function getGroupSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id'    => 'attribute_{{id}}_group',
                'class' => 'select select-attribute-option-type required-option-select'
            ))
            ->setName('attribute[{{id}}][group]')
            ->setOptions($this->getConfig()->getAttributeGroupsOptions());

        return $select->getHtml();
    }

    public function getTemplatesHtml()
    {
        $templates = array();
        foreach ($this->getConfig()->getAttributeRenderers() as $renderer) {
            $templates[] = $this->getLayout()->createBlock($renderer)->toHtml();
        }
        return implode("\n", $templates);
    }

    public function getOptionValues()
    {
        $optionsArr = array_reverse((array)$this->getType()->getAttribute(), true);

        if (!$this->_values) {
            $values = array();
            $id = 1;
            foreach ($optionsArr as $code => $attribute) {
                $value = $attribute;
                $value['code'] = $code;
                $value['id'] = $id;

                if ($this->getType()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($id, 'label');
                    $value['checkboxScopeSort']  = $this->getCheckboxScopeHtml($id, 'sort_order');
                    $value['scopeTitleDisabled'] = 'disabled';
                    $value['scopeSortDisabled']  = 'disabled';
                }
                $values[] = new Varien_Object($value);
                $id++;
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

    public function getCheckboxScopeHtml($id, $name, $checked=true, $select_id='-1')
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($select_id != '-1') {
            $selectNameHtml = '[values]['.$select_id.']';
            $selectIdHtml = 'select_'.$select_id.'_';
        }
        $checkbox = '<div><input type="checkbox" id="attribute_'.$id.'_'.$selectIdHtml.$name.'_use_default" class="attribute-option-scope-checkbox" name="'.$this->getFieldName().'['.$id.']'.$selectNameHtml.'[scope]['.$name.']" value="1" '.$checkedHtml.'/>';
        $checkbox .= '<label class="normal" for="attribute_'.$id.'_'.$selectIdHtml.$name.'_use_default">Use Default Value</label></div>';
        return $checkbox;
    }
}

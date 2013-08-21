<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Instance of gift registry type model
     */
    protected $_typeInstance;

    protected $_template = 'edit/attributes.phtml';

    /**
     * Preparing block layout
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_Registry
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Add Attribute'),
            'class' => 'action-add',
            'id'    => $this->getFieldPrefix() . '_add_new_attribute'
        ));

        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Delete Attribute'),
            'class' => 'action-delete delete-attribute-option'
        ));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        if (!$this->getTypeStoreId()) {
            return $this->getChildHtml('add_button');
        }
    }

    /**
     * Retrieve id of add button
     *
     * @return int
     */
    public function getAddButtonId()
    {
        return $this->getChildBlock('add_button')->getId();
    }

    /**
     * Retrieve delete button html
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        if (!$this->getTypeStoreId()) {
            return $this->getChildHtml('delete_button');
        }
    }

    /**
     * Get gift registry attribute config model
     *
     * @return Magento_GiftRegistry_Model_Attribute_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('Magento_GiftRegistry_Model_Attribute_Config');
    }

    /**
     * Get gift registry attribute type model
     *
     * @return Magento_GiftRegistry_Model_Type
     */
    public function getType()
    {
        if (!$this->_typeInstance) {
            if ($type = Mage::registry('current_giftregistry_type')) {
                $this->_typeInstance = $type;
            } else {
                $this->_typeInstance = Mage::getSingleton('Magento_GiftRegistry_Model_Type');
            }
        }
        return $this->_typeInstance;
    }

    /**
     * Retrieve store id of current registry type model
     *
     * @return int
     */
    public function getTypeStoreId()
    {
        return $this->getType()->getStoreId();
    }

    /**
     * Select element for choosing attribute type
     *
     * @return string
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                'id'    => $this->getFieldPrefix() . '_attribute_{{id}}_type',
                'class' => 'select required-entry attribute-type global-scope'
            ))
            ->setName('attributes[' . $this->getFieldPrefix() . '][{{id}}][type]')
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
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                'id'    => $this->getFieldPrefix() . '_attribute_{{id}}_group',
                'class' => 'select required-entry global-scope'
            ))
            ->setName('attributes[' . $this->getFieldPrefix() . '][{{id}}][group]')
            ->setOptions($this->getConfig()->getAttributeGroupsOptions());

        return $select->getHtml();
    }

    /**
     * Select element for choosing searcheable option
     *
     * @return string
     */
    public function getSearcheableSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                 'id'    => $this->getFieldPrefix() . '_attribute_{{id}}_is_searcheable',
                 'class' => 'select required-entry global-scope'
            ))
            ->setName('attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_searcheable]')
            ->setOptions(Mage::getSingleton('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Select element for choosing listed option
     *
     * @return string
     */
    public function getListedSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                 'id'    => $this->getFieldPrefix() . '_attribute_{{id}}_is_listed',
                 'class' => 'select required-entry global-scope'
            ))
            ->setName('attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_listed]')
            ->setOptions(Mage::getSingleton('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Select element for choosing required option
     *
     * @return string
     */
    public function getRequiredSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                 'id'    => $this->getFieldPrefix() . '_attribute_{{id}}_is_required',
                 'class' => 'select required-entry global-scope'
            ))
            ->setName('attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_required]')
            ->setOptions(Mage::getSingleton('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Prepare and return html of available attribute renderers
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $templates = array();
        $types = array('Select', 'Date', 'Country');

        foreach ($types as $type) {
            $renderer = 'Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_' . $type;
            $block = $this->getLayout()->createBlock($renderer)->setFieldPrefix($this->getFieldPrefix());
            $templates[] = $block->toHtml();
        }
        return implode("\n", $templates);
    }

    /**
     * Prepare and return attribute values
     *
     * @return array
     */
    public function getAttributeValues()
    {
        $values = array();
        $attributes = array();
        $groups = $this->getType()->getAttributes();
        $innerId = 0;

        if (is_array($groups)) {
            foreach ($groups as $group) {
                $attributes = array_merge($attributes, (array)$group);
            }
            $attributes = array_reverse($attributes, true);
        } else {
            return $values;
        }

        foreach ($attributes as $code => $attribute) {
            $value = $attribute;
            $value['code'] = $code;
            $value['id'] = $innerId;
            $value['prefix'] = $this->getFieldPrefix();

            if ($this->getType()->getStoreId() != '0') {
                $value['checkbox_scope'] = $this->getCheckboxScopeHtml($innerId, 'label', !isset($value['default_label']));
                $value['label_disabled'] = isset($value['default_label']) ? false : true;
            }
            if (isset($value['options']) && is_array($value['options'])) {
                $selectId = 0;
                $defaultCode = (isset($value['default'])) ? $value['default'] : '';
                foreach($value['options'] as $option) {
                    $optionData = array(
                        'code'  => $option['code'],
                        'label' => $option['label'],
                        'id' => $innerId,
                        'select_id' => $selectId,
                        'checked' => ($option['code'] == $defaultCode) ? 'checked="checked"' : ''
                    );

                    if ($this->getType()->getStoreId() != '0') {
                        $optionData['checkbox_scope'] = $this->getCheckboxScopeHtml($innerId, 'label', !isset($option['default_label']), $selectId);
                        $optionData['label_disabled'] = isset($option['default_label']) ? false : true;
                    }
                    $value['items'][] = $optionData;
                    $selectId++;
                }
            }
            if (isset($value['frontend']) && is_array($value['frontend'])) {
                foreach($value['frontend'] as $param => $paramValue) {
                    $value[$param] = $paramValue;
                }
            }

            $values[] = new Magento_Object($value);
            $innerId++;
        }
        return $values;
    }

    /**
     * Checkbox html for store scope
     *
     * @param int $id
     * @param string $name
     * @param bool $checked
     * @param int|null $selectId
     * @return string
     */
    public function getCheckboxScopeHtml($id, $name, $checked=true, $selectId=null)
    {
        $selectNameHtml = '';
        $selectIdHtml = '';
        $checkedHtml = ($checked) ? ' checked="checked"' : '';
        $elementLabel = __('Use Default Value');

        if (!is_null($selectId)) {
            $selectNameHtml = '[options]['.$selectId.']';
            $selectIdHtml = 'select_'.$selectId.'_';
        }

        $checkbox = '<div><input type="checkbox" id="'.$this->getFieldPrefix().'_attribute_'.$id.'_'.$selectIdHtml.$name.'_use_default" class="attribute-option-scope-checkbox" name="attributes['.$this->getFieldPrefix().']['.$id.']'.$selectNameHtml.'[use_default]['.$name.']" value="1" '.$checkedHtml.'/>';
        $checkbox .= '<label class="normal" for="'.$this->getFieldPrefix().'_attribute_'.$id.'_'.$selectIdHtml.$name.'_use_default"> '.$elementLabel.'</label></div>';
        return $checkbox;
    }

    /**
     * Prepare and return static types as Magento_Object
     *
     * @return array
     */
    public function getStaticTypes()
    {
        return new Magento_Object($this->getConfig()->getStaticTypes());
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute;

class Attribute extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Instance of gift registry type model
     *
     * @var \Magento\GiftRegistry\Model\Type
     */
    protected $_typeInstance;

    /**
     * @var \Magento\GiftRegistry\Model\Type
     */
    protected $defaultTypeInstance;

    /**
     * @var \Magento\GiftRegistry\Model\Attribute\Config
     */
    protected $attributeConfig;

    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $sourceYesNo;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'edit/attributes.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo
     * @param \Magento\GiftRegistry\Model\Type $defaultTypeInstance
     * @param \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Backend\Model\Config\Source\Yesno $sourceYesNo,
        \Magento\GiftRegistry\Model\Type $defaultTypeInstance,
        \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->sourceYesNo = $sourceYesNo;
        $this->defaultTypeInstance = $defaultTypeInstance;
        $this->attributeConfig = $attributeConfig;

        parent::__construct($context, $data);
    }

    /**
     * Preparing block layout
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\Registry
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Attribute'),
                'class' => 'action-add',
                'id' => $this->getFieldPrefix() . '_add_new_attribute'
            )
        );

        $this->addChild(
            'delete_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Delete Attribute'), 'class' => 'action-delete delete-attribute-option')
        );
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
     * @return \Magento\GiftRegistry\Model\Attribute\Config
     */
    public function getConfig()
    {
        return $this->attributeConfig;
    }

    /**
     * Get gift registry attribute type model
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function getType()
    {
        if (!$this->_typeInstance) {
            $type = $this->_coreRegistry->registry('current_giftregistry_type');
            if ($type) {
                $this->_typeInstance = $type;
            } else {
                $this->_typeInstance = $this->defaultTypeInstance;
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
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldPrefix() . '_attribute_{{id}}_type',
                'class' => 'select required-entry attribute-type global-scope'
            )
        )->setName(
            'attributes[' . $this->getFieldPrefix() . '][{{id}}][type]'
        )->setOptions(
            $this->getConfig()->getAttributeTypesOptions()
        );

        return $select->getHtml();
    }

    /**
     * Select element for choosing attribute group
     *
     * @return string
     */
    public function getGroupSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldPrefix() . '_attribute_{{id}}_group',
                'class' => 'select required-entry global-scope'
            )
        )->setName(
            'attributes[' . $this->getFieldPrefix() . '][{{id}}][group]'
        )->setOptions(
            $this->getConfig()->getAttributeGroupsOptions()
        );

        return $select->getHtml();
    }

    /**
     * Select element for choosing searcheable option
     *
     * @return string
     */
    public function getSearcheableSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldPrefix() . '_attribute_{{id}}_is_searcheable',
                'class' => 'select required-entry global-scope'
            )
        )->setName(
            'attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_searcheable]'
        )->setOptions(
            $this->sourceYesNo->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * Select element for choosing listed option
     *
     * @return string
     */
    public function getListedSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldPrefix() . '_attribute_{{id}}_is_listed',
                'class' => 'select required-entry global-scope'
            )
        )->setName(
            'attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_listed]'
        )->setOptions(
            $this->sourceYesNo->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * Select element for choosing required option
     *
     * @return string
     */
    public function getRequiredSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldPrefix() . '_attribute_{{id}}_is_required',
                'class' => 'select required-entry global-scope'
            )
        )->setName(
            'attributes[' . $this->getFieldPrefix() . '][{{id}}][frontend][is_required]'
        )->setOptions(
            $this->sourceYesNo->toOptionArray()
        );

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
            $renderer = 'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute\Type\\' . $type;
            $block = $this->getLayout()->createBlock($renderer)->setFieldPrefix($this->getFieldPrefix());
            $templates[] = $block->toHtml();
        }
        return implode("\n", $templates);
    }

    /**
     * Prepare and return attribute values
     *
     * @return \Magento\Object[]
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
                $value['checkbox_scope'] = $this->getCheckboxScopeHtml(
                    $innerId,
                    'label',
                    !isset($value['default_label'])
                );
                $value['label_disabled'] = isset($value['default_label']) ? false : true;
            }
            if (isset($value['options']) && is_array($value['options'])) {
                $selectId = 0;
                $defaultCode = isset($value['default']) ? $value['default'] : '';
                foreach ($value['options'] as $option) {
                    $optionData = array(
                        'code' => $option['code'],
                        'label' => $option['label'],
                        'id' => $innerId,
                        'select_id' => $selectId,
                        'checked' => $option['code'] == $defaultCode ? 'checked="checked"' : ''
                    );

                    if ($this->getType()->getStoreId() != '0') {
                        $optionData['checkbox_scope'] = $this->getCheckboxScopeHtml(
                            $innerId,
                            'label',
                            !isset($option['default_label']),
                            $selectId
                        );
                        $optionData['label_disabled'] = isset($option['default_label']) ? false : true;
                    }
                    $value['items'][] = $optionData;
                    $selectId++;
                }
            }
            if (isset($value['frontend']) && is_array($value['frontend'])) {
                foreach ($value['frontend'] as $param => $paramValue) {
                    $value[$param] = $paramValue;
                }
            }

            $values[] = new \Magento\Object($value);
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
    public function getCheckboxScopeHtml($id, $name, $checked = true, $selectId = null)
    {
        $selectNameHtml = '';
        $selectIdHtml = '';
        $checkedHtml = $checked ? ' checked="checked"' : '';
        $elementLabel = __('Use Default Value');

        if (!is_null($selectId)) {
            $selectNameHtml = '[options][' . $selectId . ']';
            $selectIdHtml = 'select_' . $selectId . '_';
        }

        $checkbox = '<div><input type="checkbox" id="' .
            $this->getFieldPrefix() .
            '_attribute_' .
            $id .
            '_' .
            $selectIdHtml .
            $name .
            '_use_default" class="attribute-option-scope-checkbox" name="attributes[' .
            $this->getFieldPrefix() .
            '][' .
            $id .
            ']' .
            $selectNameHtml .
            '[use_default][' .
            $name .
            ']" value="1" ' .
            $checkedHtml .
            '/>' .
            '<label class="normal" for="' .
            $this->getFieldPrefix() .
            '_attribute_' .
            $id .
            '_' .
            $selectIdHtml .
            $name .
            '_use_default"> ' .
            $elementLabel .
            '</label></div>';
        return $checkbox;
    }

    /**
     * Prepare and return static types as \Magento\Object
     *
     * @return \Magento\Object
     */
    public function getStaticTypes()
    {
        return new \Magento\Object($this->getConfig()->getStaticTypes());
    }
}

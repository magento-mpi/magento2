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
 * Product attribute add/edit form main tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab;

class Advanced extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Eav data
     *
     * @var \Magento\Eav\Helper\Data
     */
    protected $_eavData = null;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    /**
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->_eavData = $eavData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding product form elements for editing attribute
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab\Advanced
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        $form = $this->_formFactory->create(array('attributes' => array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        )));

        $fieldset = $form->addFieldset(
            'advanced_fieldset',
            array(
                'legend' => __('Advanced Attribute Properties'),
                'collapsable' => true
            )
        );

        $yesno = \Mage::getModel('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray();

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField(
            'attribute_code',
            'text',
            array(
                'name' => 'attribute_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
                'note' => __(
                    'For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %1 symbols',
                    \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
            )
        );

        $fieldset->addField(
            'default_value_text',
            'text',
            array(
                'name' => 'default_value_text',
                'label' => __('Default Value'),
                'title' => __('Default Value'),
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $fieldset->addField(
            'default_value_yesno',
            'select',
            array(
                'name' => 'default_value_yesno',
                'label' => __('Default Value'),
                'title' => __('Default Value'),
                'values' => $yesno,
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField(
            'default_value_date',
            'date',
            array(
                'name' => 'default_value_date',
                'label' => __('Default Value'),
                'title' => __('Default Value'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'value' => $attributeObject->getDefaultValue(),
                'date_format' => $dateFormat
            )
        );

        $fieldset->addField(
            'default_value_textarea',
            'textarea',
            array(
                'name' => 'default_value_textarea',
                'label' => __('Default Value'),
                'title' => __('Default Value'),
                'value' => $attributeObject->getDefaultValue(),
            )
        );

        $fieldset->addField(
            'is_unique',
            'select',
            array(
                'name' => 'is_unique',
                'label' => __('Unique Value'),
                'title' => __('Unique Value (not shared with other products)'),
                'note' => __('Not shared with other products'),
                'values' => $yesno,
            )
        );

        $fieldset->addField(
            'frontend_class',
            'select',
            array(
                'name' => 'frontend_class',
                'label' => __('Input Validation for Store Owner'),
                'title' => __('Input Validation for Store Owner'),
                'values' => $this->_eavData->getFrontendClasses(
                    $attributeObject->getEntityType()->getEntityTypeCode()
                )
            )
        );

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $yesnoSource = \Mage::getModel('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray();

        $scopes = array(
            \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE =>__('Store View'),
            \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE =>__('Website'),
            \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL =>__('Global'),
        );

        if ($attributeObject->getAttributeCode() == 'status' || $attributeObject->getAttributeCode() == 'tax_class_id') {
            unset($scopes[\Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => __('Scope'),
            'title' => __('Scope'),
            'note'  => __('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');


        $fieldset->addField('is_configurable', 'select', array(
            'name' => 'is_configurable',
            'label' => __('Use To Create Configurable Product'),
            'values' => $yesnoSource,
        ));
        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab\Advanced
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private function getAttributeObject()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }
}

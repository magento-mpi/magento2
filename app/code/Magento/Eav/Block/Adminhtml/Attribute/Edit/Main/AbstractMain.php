<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product attribute add/edit form main tab
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Block\Adminhtml\Attribute\Edit\Main;

abstract class AbstractMain
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_attribute = null;

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
     * @var \Magento\Eav\Model\Entity\Attribute\Config
     */
    protected $_attributeConfig;
    
    /**
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_eavData = $eavData;
        $this->_attributeConfig = $attributeConfig;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    public function setAttributeObject($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    public function getAttributeObject()
    {
        if (null === $this->_attribute) {
            return $this->_coreRegistry->registry('entity_attribute');
        }
        return $this->_attribute;
    }

    /**
     * Preparing default form elements for editing attribute
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => __('Attribute Properties'))
        );

        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = \Mage::getModel('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray();

        $labels = $attributeObject->getFrontendLabel();
        $fieldset->addField(
            'attribute_label',
            'text',
            array(
                'name' => 'frontend_label[0]',
                'label' => __('Attribute Label'),
                'title' => __('Attribute Label'),
                'required' => true,
                'value' => is_array($labels) ? $labels[0] : $labels
            )
        );


        $validateClass = sprintf('validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'note'  => __('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %1 symbols', \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ));

        $inputTypes = \Mage::getModel('Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype')->toOptionArray();

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => __('Catalog Input Type for Store Owner'),
            'title' => __('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $fieldset->addField(
            'is_required',
            'select',
            array(
                'name' => 'is_required',
                'label' => __('Values Required'),
                'title' => __('Values Required'),
                'values' => $yesno,
            )
        );

        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue(),
        ));

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', array(
            'name'   => 'default_value_date',
            'label'  => __('Default Value'),
            'title'  => __('Default Value'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'value'  => $attributeObject->getDefaultValue(),
            'date_format' => $dateFormat
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

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

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => __('Input Validation for Store Owner'),
            'title' => __('Input Validation for Store Owner'),
            'values'=> $this->_eavData->getFrontendClasses(
                $attributeObject->getEntityType()->getEntityTypeCode()
            )
        ));

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
     */
    protected function _initFormValues()
    {
        $this->_eventManager->dispatch('adminhtml_block_eav_attribute_edit_form_init', array(
            'form' => $this->getForm(),
        ));
        $this->getForm()
            ->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $attributeObject = $this->getAttributeObject();
        if ($attributeObject->getId()) {
            $form = $this->getForm();
            foreach ($this->_attributeConfig->getLockedFields($attributeObject) as $field) {
                if ($element = $form->getElement($field)) {
                    $element->setDisabled(1);
                    $element->setReadonly(1);
                }
            }
        }
        return $this;
    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()
            ->createBlock('Magento\Eav\Block\Adminhtml\Attribute\Edit\Js')->toHtml();
        return $html . $jsScripts;
    }
}

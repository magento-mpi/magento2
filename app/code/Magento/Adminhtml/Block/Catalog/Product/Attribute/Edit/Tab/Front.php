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
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Front extends Magento_Backend_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     * @return $this
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');

        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $yesnoSource = Mage::getModel('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray();;

        $fieldset = $form->addFieldset(
            'front_fieldset',
            array(
                'legend'=>__('Frontend Properties'),
                'collapsable' => $this->getRequest()->has('popup'),
            )
        );

        $fieldset->addField('is_searchable', 'select', array(
            'name'     => 'is_searchable',
            'label'    => __('Use in Quick Search'),
            'title'    => __('Use in Quick Search'),
            'values'   => $yesnoSource,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => __('Use in Advanced Search'),
            'title' => __('Use in Advanced Search'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => __('Comparable on Frontend'),
            'title' => __('Comparable on Frontend'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => __("Use In Layered Navigation"),
            'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => array(
                array('value' => '0', 'label' => __('No')),
                array('value' => '1', 'label' => __('Filterable (with results)')),
                array('value' => '2', 'label' => __('Filterable (no results)')),
            ),
        ));

        $fieldset->addField('is_filterable_in_search', 'select', array(
            'name' => 'is_filterable_in_search',
            'label' => __("Use In Search Results Layered Navigation"),
            'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_used_for_promo_rules', 'select', array(
            'name' => 'is_used_for_promo_rules',
            'label' => __('Use for Promo Rule Conditions'),
            'title' => __('Use for Promo Rule Conditions'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => __('Position'),
            'title' => __('Position in Layered Navigation'),
            'note' => __('Position of attribute in layered navigation block'),
            'class' => 'validate-digits'
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => __('Enable WYSIWYG'),
            'title' => __('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', array(
            'name' => 'is_html_allowed_on_front',
            'label' => __('Allow HTML Tags on Frontend'),
            'title' => __('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ));
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField('is_visible_on_front', 'select', array(
            'name'      => 'is_visible_on_front',
            'label'     => __('Visible on Catalog Pages on Frontend'),
            'title'     => __('Visible on Catalog Pages on Frontend'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('used_in_product_listing', 'select', array(
            'name'      => 'used_in_product_listing',
            'label'     => __('Used in Product Listing'),
            'title'     => __('Used in Product Listing'),
            'note'      => __('Depends on design theme'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('used_for_sort_by', 'select', array(
            'name'      => 'used_for_sort_by',
            'label'     => __('Used for Sorting in Product Listing'),
            'title'     => __('Used for Sorting in Product Listing'),
            'note'      => __('Depends on design theme'),
            'values'    => $yesnoSource,
        ));

        // define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Form_Element_Dependence')
                ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
                ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
                ->addFieldMap("frontend_input", 'frontend_input_type')
                ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
                ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );

        $form->setValues($attributeObject->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}

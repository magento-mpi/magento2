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
 * New category creation form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategory extends Mage_Backend_Block_Widget_Form
{
    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }


    /**
     * Form preparation
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'new_category_form'));
        $form->setUseContainer($this->getUseContainer());

        $form->addField('new_category_messages', 'note', array());

        $fieldset = $form->addFieldset('new_category_form_fieldset', array());

        $fieldset->addField('new_category_name', 'text', array(
            'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Category Name'),
            'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Category Name'),
            'required' => true,
            'name'     => 'new_category_name',
        ));

        $fieldset->addField('new_category_parent', 'select', array(
            'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'required' => true,
            'options'  => array(),
            'class'    => 'validate-parent-category',
            'name'     => 'new_category_parent',
        ));

        $this->setForm($form);
    }

    /**
     * Category save action URL
     *
     * @return string
     */
    public function getSaveCategoryUrl()
    {
        return $this->getUrl('adminhtml/catalog_category/save');
    }

    /**
     * Attach new category dialog widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('Mage_Core_Helper_Data');
        $widgetOptions = $coreHelper->jsonEncode(array(
            'suggestOptions' => array(
                'source' => $this->getUrl('adminhtml/catalog_category/suggestCategories'),
                'valueField' => '#new_category_parent',
                'className' => 'category-select',
                'multiselect' => true,
                'showAll' => true,
            ),
            'saveCategoryUrl' => $this->getUrl('adminhtml/catalog_category/save'),
        ));
        return <<<HTML
<script>
    jQuery(function($) { // waiting for page to load to have '#category_ids-template' available
        $('#new-category').mage('newCategoryDialog', $widgetOptions);
    });
</script>
HTML;
    }
}

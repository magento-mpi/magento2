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
     * Form preparation
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->addField('new_category_messages', 'note', array());

        $fieldset = $form->addFieldset('new_category_form', array());

        $fieldset->addField('new_category_name', 'text', array(
            'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Category Name'),
            'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Category Name'),
            'required' => true,
        ));

        $fieldset->addField('new_category_parent', 'select', array(
            'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'required' => true,
            'options'  => array(),
            'class'    => 'validate-parent-category',
        ));

        $this->setForm($form);
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
        $widgetUrl = $coreHelper->jsonEncode($this->getViewFileUrl('Mage_Catalog::js/new-category-dialog.js'));
        $widgetOptions = $coreHelper->jsonEncode(array(
            'suggestOptions' => array(
                'source' => $this->getUrl('*/catalog_category/suggestCategories'),
                'valueField' => '#new_category_parent',
                'template' => '#category_ids-template',
                'control' => 'jstree',
                'multiselect' => true,
                'className' => 'category-select',
            ),
            'saveCategoryUrl' => $this->getUrl('*/catalog_category/save'),
        ));
        return <<<HTML
<script>
    head.js($widgetUrl, function () {
        jQuery(function($) { // waiting for page to load to have '#category_ids-template' available
            $('#new-category').mage('newCategoryDialog', $widgetOptions);
        });
    });
</script>
HTML;
    }
}

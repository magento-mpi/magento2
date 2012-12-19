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

        $fieldset->addField('new_category_parent', 'text', array(
            'label'        => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'title'        => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
            'autocomplete' => 'off',
            'required'     => true,
            'class'        => 'validate-parent-category',
        ));

        $fieldset->addField('new_category_parent_id', 'hidden', array());

        $this->setForm($form);
    }

    /**
     * Category save action URL
     *
     * @return string
     */
    public function getSaveCategoryUrl()
    {
        return $this->getUrl('*/catalog_category/save');
    }

    /**
     * Category suggestion action URL
     *
     * @return string
     */
    public function getSuggestCategoryUrl()
    {
        return $this->getUrl('*/catalog_category/suggestCategories');
    }
}

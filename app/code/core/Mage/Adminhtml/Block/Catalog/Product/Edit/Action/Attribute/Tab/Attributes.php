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
 * Adminhtml catalog product edit action attributes update tab block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    protected function _prepareForm()
    {
        $this->setFormExcludedFieldList(array(
            'category_ids',
            'gallery',
            'group_price',
            'image',
            'media_gallery',
            'quantity_and_stock_status',
            'recurring_profile',
            'tier_price',
        ));
        Mage::dispatchEvent('adminhtml_catalog_product_form_prepare_excluded_field_list', array('object'=>$this));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', array(
            'legend' => Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes'),
        ));
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::getModel('Mage_Catalog_Model_Product'));
        $this->_setFieldset($attributes, $fieldset, $this->getFormExcludedFieldList());
        $form->setFieldNameSuffix('attributes');
        $this->setForm($form);
    }

    /**
     * Retrive attributes for product massupdate
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->helper('Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute')
            ->getAttributes()->getItems();
    }

    /**
     * Additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'price' => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price',
            'weight' => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight',
            'image' => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image',
            'boolean' => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean',
        );
    }

    /**
     * Custom additional element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId = $element->getId();
        $checkboxLabel = Mage::helper('Mage_Catalog_Helper_Data')->__('Change');
        $html = <<<HTML
<span class="attribute-change-checkbox">
    <label>
        <input type="checkbox" $nameAttributeHtml onclick="toogleFieldEditMode(this, '{$elementId}')" />
        {$checkboxLabel}
    </label>
</span>
<script>initDisableFields("{$elementId}")</script>
HTML;
        if ($elementId === 'weight') {
            $html .= <<<HTML
<script>jQuery(function($) {
    $('#weight_and_type_switcher, label[for=weight_and_type_switcher]').hide();
});</script>
HTML;
        }
        return $html;
    }

    public function getTabLabel()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes');
    }

    public function getTabTitle()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Attributes');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}

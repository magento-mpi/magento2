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
 * Adminhtml catalog product edit action attributes update tab block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
    extends Magento_Adminhtml_Block_Catalog_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Adminhtml catalog product edit action attribute
     *
     * @var Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected $_adminhtmlCatalogProductEditActionAttribute = null;

    /**
     * @param Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     * $adminhtmlCatalogProductEditActionAttribute
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute $adminhtmlCatalogProductEditActionAttribute,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlCatalogProductEditActionAttribute = $adminhtmlCatalogProductEditActionAttribute;
        parent::__construct($coreData, $context, $data);
    }

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

        $form = new Magento_Data_Form();
        $fieldset = $form->addFieldset('fields', array(
            'legend' => __('Attributes'),
        ));
        $attributes = $this->getAttributes();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::getModel('Magento_Catalog_Model_Product'));
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
        return $this->_adminhtmlCatalogProductEditActionAttribute
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
            'price' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Price',
            'weight' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight',
            'image' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Image',
            'boolean' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean',
        );
    }

    /**
     * Custom additional element html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId = $element->getId();
        $checkboxLabel = __('Change');
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
        return __('Attributes');
    }

    public function getTabTitle()
    {
        return __('Attributes');
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

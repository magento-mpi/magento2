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
 * Create Configurable product Settings Tab Block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare block children and data
     *
     */
    protected function _prepareLayout()
    {
        $onclick = "jQuery('[data-form=edit-product]').attr('action', "
            . $this->helper('Magento_Core_Helper_Data')->jsonEncode($this->getContinueUrl())
            . ").addClass('ignore-validate').submit();";
        $this->addChild('continue_button', 'Magento_Backend_Block_Widget_Button', array(
            'label'   => __('Generate Variations'),
            'onclick' => $onclick,
            'class'   => 'save',
        ));
        parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();
        $fieldset = $form->addFieldset('settings', array(
            'legend' => __('Select Configurable Attributes')
        ));

        $fieldset->addField('configurable-attribute-selector', 'text', array(
            'label' => 'Select Attribute',
            'title' => 'Select Attribute',
        ));

        $product = $this->getProduct();
        /** @var $configurableType Magento_Catalog_Model_Product_Type_Configurable */
        $configurableType = Mage::getSingleton('Magento_Catalog_Model_Product_Type_Configurable');
        $usedAttributes = $product->isConfigurable()
            ? $configurableType->getUsedProductAttributes($product)
            : array();
        foreach ($usedAttributes as $attribute) {
            /** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            if ($configurableType->canUseAttribute($attribute, $product)) {
                $fieldset->addField('attribute_' . $attribute->getAttributeId(), 'checkbox', array(
                    'label' => $attribute->getFrontendLabel(),
                    'title' => $attribute->getFrontendLabel(),
                    'name'  => 'attributes[]',
                    'class' => 'configurable-attribute-checkbox',
                    'value' => $attribute->getAttributeId(),
                    'checked' => true
                ));
            }
        }

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Continue URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl($this->getProduct()->getId() ? '*/*/edit' : '*/*/new', array(
            '_current' => true,
        ));
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('set' => null, 'type' => null));
    }
}

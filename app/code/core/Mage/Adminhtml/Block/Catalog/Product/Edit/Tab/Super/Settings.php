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
 * Create Configuranle procuct Settings Tab Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare block children and data
     *
     */
    protected function _prepareLayout()
    {
        $onclick = "setSuperSettings('" . $this->getContinueUrl() . "','attribute-checkbox', 'attributes')";
        $this->addChild('continue_button', 'Mage_Backend_Block_Widget_Button', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Apply'),
            'onclick' => $onclick,
            'class' => 'save',
        ));

        $this->addChild('back_button', 'Mage_Backend_Block_Widget_Button', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
            'onclick' => "setLocation('" . $this->getBackUrl() . "')",
            'class' => 'back'
        ));

        parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array(
            'legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Select Configurable Attributes ')
        ));

        $product    = $this->_getProduct();
        $attributes = $product->getTypeInstance()
            ->getSetAttributes($product);

        $hasAttributes = false;

        if ($product->isConfigurable()) {
            $usedAttributes = $this->_getProduct()->getTypeInstance()
             ->getUsedProductAttributeIds($this->_getProduct());
        } else {
            $usedAttributes = array();
        }

        $configurableType = Mage::getSingleton('Mage_Catalog_Model_Product_Type_Configurable');

        foreach ($attributes as $attribute) {
            if ($configurableType->canUseAttribute($attribute, $product)) {
                $hasAttributes = true;
                $fieldset->addField('attribute_'.$attribute->getAttributeId(), 'checkbox', array(
                    'label' => $attribute->getFrontend()->getLabel(),
                    'title' => $attribute->getFrontend()->getLabel(),
                    'name'  => 'attribute',
                    'class' => 'attribute-checkbox',
                    'value' => $attribute->getAttributeId(),
                    'checked' => in_array($attribute->getAttributeId(), $usedAttributes)
                ));
            }
        }

        if ($hasAttributes) {
            $fieldset->addField('attributes', 'hidden', array(
                'name'  => 'attribute_validate',
                'value' => '',
                'class' => 'validate-super-product-attributes'
            ));

            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }
        else {
            $fieldset->addField('note_text', 'note', array(
                'text' => $this->__('This attribute set does not have attributes which we can use for configurable product')
            ));
            $fieldset->addField('back_button', 'note', array(
                'text' => $this->getChildHtml('back_button'),
            ));
        }


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
        return $this->getUrl($this->_getProduct()->getId() ? '*/*/edit' : '*/*/new', array(
            '_current'   => true,
            'attributes' => '{{attributes}}'
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

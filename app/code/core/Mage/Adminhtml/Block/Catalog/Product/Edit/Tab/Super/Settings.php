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
        $onclick = "setSuperSettings('".$this->getContinueUrl()."','attribute-checkbox', 'attributes')";
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Continue'),
                    'onclick'   => $onclick,
                    'class'     => 'save'
                ))
        );

        $backButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
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
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        $fieldset->addField('req_text', 'note', array(
            'text' => '<ul class="messages"><li class="notice-msg"><ul><li>'
                    .  $this->__('Only attributes with scope "Global", input type "Dropdown" and Use To Create Configurable Product "Yes" are available.')
                    . '</li></ul></li></ul>'
        ));

        $hasAttributes = false;

        foreach ($attributes as $attribute) {
            if ($product->getTypeInstance(true)->canUseAttribute($attribute, $product)) {
                $hasAttributes = true;
                $fieldset->addField('attribute_'.$attribute->getAttributeId(), 'checkbox', array(
                    'label' => $attribute->getFrontend()->getLabel(),
                    'title' => $attribute->getFrontend()->getLabel(),
                    'name'  => 'attribute',
                    'class' => 'attribute-checkbox',
                    'value' => $attribute->getAttributeId()
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
        return $this->getUrl('*/*/new', array(
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
        return $this->getUrl('*/*/new', array('set'=>null, 'type'=>null));
    }
}

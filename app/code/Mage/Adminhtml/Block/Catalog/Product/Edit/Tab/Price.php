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
 * Adminhtml product edit price block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('tiered_price', array('legend'=>__('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> __('Default Price'),
                'title'=> __('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier')
        );

        $this->setForm($form);
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price END

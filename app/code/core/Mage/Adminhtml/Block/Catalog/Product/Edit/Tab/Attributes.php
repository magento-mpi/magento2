<?php
/**
 * Product attributes tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form 
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }
    protected function _prepareForm()
    {
        if ($group = $this->getGroup()) {
            $form = new Varien_Data_Form();
            $fieldset = $form->addFieldset('group_fields'.$group->getId(), array('legend'=>__($group->getAttributeGroupName())));
            $attributes = Mage::registry('product')->getAttributes($group->getId(),true);
            
            if (Mage::registry('product')->isSuper()) {
                foreach ($attributes as $index => $attribute) {
                	if (!$attribute->getUseInSuperProduct()) {
                	    unset($attributes[$index]);
                	}
                }
            }
            
            $this->_setFieldset($attributes, $fieldset);
            
            if ($tierPrice = $form->getElement('tier_price')) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                );          
            }

            if ($gallery = $form->getElement('gallery')) {
                $gallery->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/widget_form_element_gallery')
                );          
            }

            $form->addValues(Mage::registry('product')->getData());
            $form->setFieldNameSuffix('product');
            $this->setForm($form);
        }
    }
    
    protected function _getAdditionalElementTypes()
    {
        return array(
            'price' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean')
        );
    }
}

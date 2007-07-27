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
	protected function _prepareForm()
	{
	    if ($group = $this->getGroup()) {
    		$form = new Varien_Data_Form();
    		$fieldset = $form->addFieldset('grop_fields', array('legend'=>__($group->getAttributeGroupName())));
    		$fieldset->addType('image', Mage::getConfig()->getBlockClassName('adminhtml/catalog_category_form_image'));
    		
    		$this->_setFieldset(Mage::registry('product')->getAttributes($group->getId()), $fieldset);
    		
    		if ($tierPrice = $form->getElement('tier_price')) {
                $tierPrice->setRenderer(
        			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
        		);    		
    		}
    		
    		$form->addValues(Mage::registry('product')->getData());
    		$form->setFieldNameSuffix('product');
    		$this->setForm($form);
	    }
	}
}

<?php
/**
 * Adminhtml product edit price block
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$product = Mage::registry('product');
		
		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('tiered_price', array('legend'=>__('Tier Pricing')));
		
		$fieldset->addField('tier_price', 'text', array(
				'name'=>'tier_price',
				'class'=>'requried-entry',
				'value'=>$product->getData('tier_price')
		));
		
				
		$form->getElement('tier_price')->setRenderer(
			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
		);
		
		$this->setForm($form);
	}
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price END
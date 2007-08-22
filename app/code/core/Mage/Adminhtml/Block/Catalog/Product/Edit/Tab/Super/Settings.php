<?php
/**
 * Create super product settings tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings extends Mage_Adminhtml_Block_Widget_Form 
{		
    protected function _initChildren()
    {
        $this->setChild('continue_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Continue'),
                    'onclick'   => "setSuperSettings('".$this->getContinueUrl()."','attribute-checkbox', 'attributes')",
                    'class'     => 'save'
					))
				);
    }
    
    protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('settings', array('legend'=>__('Create Super Product Settings')));
		
		$entityType = Mage::registry('product')->getResource()->getConfig();
		
		$fieldset->addField('attributes', 'hidden', array(
		            'name'  => 'attribute_validate',
		            'value' => '',
		            'class'	=> 'validate-super-product-attributes'
				));
		
		$product = Mage::registry('product');
		$attributes = $product->getAttributes();
		foreach($attributes as $attribute) {
			if($product->canUseAttributeForSuperProduct($attribute)) {
				$fieldset->addField('attribute_'.$attribute->getAttributeId(), 'checkbox', array(
		            'label' => __($attribute->getFrontend()->getLabel()),
		            'title' => __($attribute->getFrontend()->getLabel()),
		            'name'  => 'attribute',
		            'class' => 'attribute-checkbox',
		            'value' => $attribute->getAttributeId()
				));
			}
		}
		
		
				
		$fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
		));
		
		$this->setForm($form);
	}
	
	public function getContinueUrl()
	{
	    return Mage::getUrl('*/*/new', array('_current'=>true));
	}
}

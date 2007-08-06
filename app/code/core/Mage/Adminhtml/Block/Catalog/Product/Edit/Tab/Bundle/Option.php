<?php
/**
 * Product bundle options form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Bundle_Option extends Mage_Adminhtml_Block_Widget_Form 
{
	protected $_parent = null;
	
	public function setParent(Mage_Core_Block_Abstract $parent)
	{
		$this->_parent = $parent;
		return $this;
	}
	
	public function getParent()
	{
		return $this->_parent;
	}
	
	
    protected function _prepareForm()
    {
    	$form = new Varien_Data_Form();
    	
    	$fieldset = $form->addFieldset('fieldset', array(
    		'legend' => __('Options')
    	));
    	
    	$fieldset->addField('options', 'text', 
    		array(
    			'name'=>'b_option',
    			'class'=>'required-entry'
    		)
    	);
    	
    	
    	$form->getElement('options')->setRenderer(
    		$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_bundle_option_renderer')
    			->setParent($this)
      	);
    	
    	$this->setForm($form);
    	return $this;
    }
}

<?php
/**
 * Adminhtml tier pricing item renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Bundle_Option_Renderer extends Mage_Core_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_parent = null;
	protected $_element = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('catalog/product/edit/bundle/option/renderer.phtml');
		
	}
	public function setElement(Varien_Data_Form_Element_Abstract $element)
	{
		$this->_element = $element;
		return $this;
	}
	
	public function getElement()
	{
		return $this->_element;
	}
	
	public function setParent(Mage_Core_Block_Abstract $parent)
	{
		$this->_parent = $parent;
		$this->_initButton();
		return $this;
	}
	
	public function getParent()
	{
		if(!$this->_parent && $this->getData('parent')) {
			$this->setParent($this->getData('parent'));
		}
		
		return $this->_parent;
	}
		
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		$element->addClass('input-text');
		return $this->toHtml();
	}
	
	protected function _initButton() // Not in _initChildren becouse I can't use any seted data in that method
	{
		$this->setChild('delete_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'id'		=> 'new__id__delete_button',
					'label'     => __('Delete Option'),
                    'onclick'   => $this->getJsObjectName().".deleteItem('#{index}')",
                    'class' => 'delete'
				)));
				
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => __('Add Option'),
                    'onclick'   => $this->getJsObjectName().".addItem(null, '', 0, {})",
                    'class' => 'add'
				)));
	}
	
	public function getTemplateHtmlId()
	{
		return $this->getParent()->getParent()->getJsTemplateHtmlId();
	}
	
	public function getJsObjectName() 
	{
		return $this->getParent()->getParent()->getJsObjectName();
	}
	
	public function getContainerHtmlId()
	{
		return $this->getParent()->getParent()->getJsContainerHtmlId();
	}
	
	public function getAddButtonHtml() 
	{
		return $this->getChildHtml('add_button');
	}
	
	public function getDeleteButtonHtml() 
	{
		return $this->getChildHtml('delete_button');
	}
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier END
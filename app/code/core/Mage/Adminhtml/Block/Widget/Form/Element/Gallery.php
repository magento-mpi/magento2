<?php
/**
 * Adminhtml image gallery item renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Sergiy Lysak <sergey@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Form_Element_Gallery extends Mage_Core_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element = null;
	
	public function __construct() 
	{
		$this->setTemplate('widget/form/element/gallery.phtml');
	}
	
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
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
	
	public function getValues()
	{
		return $this->getElement()->getValue();
	}
	
	protected function _initChildren()
	{
		$this->setChild('delete_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => __('Delete'),
                    'onclick'   => "deleteImage(#image#)",
                    'class' => 'delete'
				)));
				
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => __('Add New Image'),
                    'onclick'   => 'addNewImage()',
                    'class' => 'add'
				)));
	}
	
	public function getAddButtonHtml() 
	{
		return $this->getChildHtml('add_button');
	}
	
	public function getDeleteButtonHtml($image) 
	{
		return str_replace('#image#', $image, $this->getChildHtml('delete_button'));
	}
}

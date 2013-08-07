<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend image gallery item renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Form_Element_Gallery extends Mage_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{

    protected $_element = null;

    protected $_template = 'Mage_Backend::widget/form/element/gallery.phtml';

    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(Magento_Data_Form_Element_Abstract $element)
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

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Backend_Helper_Data')->__('Delete'),
            'onclick'   => "deleteImage(#image#)",
            'class' => 'delete'
        ));

        $this->addChild('add_button', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Backend_Helper_Data')->__('Add New Image'),
            'onclick'   => 'addNewImage()',
            'class' => 'add'
        ));
        return parent::_prepareLayout();
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


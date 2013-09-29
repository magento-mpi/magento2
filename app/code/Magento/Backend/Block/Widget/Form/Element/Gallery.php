<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend image gallery item renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Form\Element;

class Gallery extends \Magento\Backend\Block\Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{

    protected $_element = null;

    protected $_template = 'Magento_Backend::widget/form/element/gallery.phtml';

    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(\Magento\Data\Form\Element\AbstractElement $element)
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
        $this->addChild('delete_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Delete'),
            'onclick'   => "deleteImage(#image#)",
            'class' => 'delete'
        ));

        $this->addChild('add_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Add New Image'),
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


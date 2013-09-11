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
 * Form fieldset default renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Form\Renderer;

class Fieldset extends \Magento\Backend\Block\Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    protected $_element;

    protected $_template = 'Magento_Backend::widget/form/renderer/fieldset.phtml';

    public function getElement()
    {
        return $this->_element;
    }

    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Color-picker form element renderer
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form;

class Renderer extends \Magento\Backend\Block\Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element to render
     *
     * @var \Magento\Data\Form\Element\AbstractElement
     */
    protected $_element;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template;

    /**
     * Get element renderer bound to
     *
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}

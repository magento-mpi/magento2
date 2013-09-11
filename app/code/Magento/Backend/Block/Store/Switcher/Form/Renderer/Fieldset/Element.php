<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form fieldset renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset;

class Element
    extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element which re-rendering
     *
     * @var \Magento\Data\Form\Element\Fieldset
     */
    protected $_element;

    protected $_template = 'store/switcher/form/renderer/fieldset/element.phtml';

    /**
     * Retrieve an element
     *
     * @return \Magento\Data\Form\Element\Fieldset
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * Return html for store switcher hint
     *
     * @return string
     */
    public function getHintHtml()
    {
        return \Mage::getBlockSingleton('\Magento\Backend\Block\Store\Switcher')->getHintHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * History output renderer to get layout update
 */
class Mage_DesignEditor_Model_History_Renderer_LayoutUpdate implements Mage_DesignEditor_Model_History_RendererInterface
{
    /**
     * Get Layout update out of collection of changes
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @param string|null $handle
     * @return string
     */
    public function render(Mage_DesignEditor_Model_Change_Collection $collection, $handle = null)
    {
        $element = new Varien_Simplexml_Element($this->_getInitialXml());

        foreach ($collection as $item) {
            if ($item instanceof Mage_DesignEditor_Model_Change_LayoutAbstract) {
                $this->_render($element, $item);
            }
        }

        if ($handle) {
            $layoutUpdate = '';
            $element = $element->$handle;
            /** @var $node Varien_Simplexml_Element */
            foreach ($element->children() as $node) {
                $layoutUpdate .= $node->asNiceXml();
            }
        } else {
            $layoutUpdate = $element->asNiceXml();
        }

        return $layoutUpdate;
    }

    /**
     * Get initial XML structure
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><layout></layout>';
    }

    /**
     * Render layout update for single layout change
     *
     * @param SimpleXMLElement $element
     * @param Mage_DesignEditor_Model_Change_LayoutAbstract $item
     * @return DOMElement
     */
    protected function _render(SimpleXMLElement $element, $item)
    {
        $handle = $this->_getHandleNode($element, $item->getData('handle'));
        $directive = $handle->addChild($item->getLayoutDirective());

        foreach ($item->getLayoutUpdateData() as $attribute => $value) {
            $directive->addAttribute($attribute, $value);
        }
        return $handle;
    }

    /**
     * Create or get existing handle node
     *
     * @param SimpleXMLElement $element
     * @param string $handle
     * @return SimpleXMLElement
     */
    protected function _getHandleNode(SimpleXMLElement $element, $handle)
    {
        $node = $element->$handle;
        if (!$node) {
            $node = $element->addChild($handle);
        }

        return $node;
    }
}

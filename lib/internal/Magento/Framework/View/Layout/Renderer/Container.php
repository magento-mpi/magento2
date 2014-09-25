<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Renderer;

use Magento\Framework\View\Layout\Element;

class Container
{
    /**
     * @var \Magento\Framework\Data\Structure
     */
    protected $structure;

    /**
     * @param \Magento\Framework\Data\Structure $structure
     */
    public function __construct(
        \Magento\Framework\Data\Structure $structure
    ) {
        $this->structure = $structure;
    }

    /**
     * Generate
     * @param string $name
     */
    public function render($name)
    {
        $this->_renderContainer($name);
    }

    /**
     * Gets HTML of container element
     *
     * @param string $name
     * @return string
     */
    protected function _renderContainer($name)
    {
        $html = '';
        $children = $this->getChildNames($name);
        foreach ($children as $child) {
            $html .= $this->renderElement($child);
        }
        if ($html == '' || !$this->structure->getAttribute($name, Element::CONTAINER_OPT_HTML_TAG)) {
            return $html;
        }

        $htmlId = $this->structure->getAttribute($name, Element::CONTAINER_OPT_HTML_ID);
        if ($htmlId) {
            $htmlId = ' id="' . $htmlId . '"';
        }

        $htmlClass = $this->structure->getAttribute($name, Element::CONTAINER_OPT_HTML_CLASS);
        if ($htmlClass) {
            $htmlClass = ' class="' . $htmlClass . '"';
        }

        $htmlTag = $this->structure->getAttribute($name, Element::CONTAINER_OPT_HTML_TAG);

        $html = sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $htmlTag, $htmlId, $htmlClass, $html);

        return $html;
    }

    /**
     * Get list of child names
     *
     * @param string $parentName
     * @return array
     */
    protected function getChildNames($parentName)
    {
        return array_keys($this->structure->getChildren($parentName));
    }
}
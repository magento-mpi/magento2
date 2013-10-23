<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Render;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle\RenderInterface;
use Magento\View\Render\Html;

class Container extends AbstractHandle implements RenderInterface
{
    /**
     * Container type
     */
    const TYPE = 'container';

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Container
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');
        $elementName = $elementName ?: ('Container-' . $this->nameIncrement++);

        if (isset($elementName)) {
            $arguments = $element = $this->parseAttributes($layoutElement);

            $element['arguments'] = $arguments;
            $element['type'] = self::TYPE;
            $element['name'] = $elementName;

            $layout->addElement($elementName, $element);

            // assign to parent element
            $this->assignToParentElement($element, $layout, $parentName);

            // parse children
            $this->parseChildren($layoutElement, $layout, $elementName);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Container
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['name']) && !isset($element['is_registered'])) {
            $elementName = $element['name'];

            $layout->updateElement($elementName, array('is_registered' => true));

            // register children
            $this->registerChildren($elementName, $layout);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @param string $type [optional]
     * @return string
     */
    public function render(array $element, LayoutInterface $layout, $parentName, $type = Html::TYPE_HTML)
    {
        $result = '';
        if (isset($element['name'])) {
            $elementName = $element['name'];

            $result = $this->renderChildren($elementName, $layout, $type);
        }

        $render = $this->renderFactory->get($type);

        $containerInfo = $this->getContainerInfo($element);

        $result = $render->renderContainer($result, $containerInfo);

        return $result;
    }
}

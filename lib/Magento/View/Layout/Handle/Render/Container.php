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

/**
 * @package Magento\View
 */
class Container extends AbstractHandle implements RenderInterface
{
    /**
     * Container type
     */
    const TYPE = 'container';

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function register(array $element, LayoutInterface $layout)
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
     * @inheritdoc
     */
    public function render($elementName, LayoutInterface $layout)
    {
        $result = $this->renderChildren($elementName, $layout);

        $render = $this->renderFactory->get(Html::TYPE_HTML);

        $containerInfo = $this->getContainerInfo($elementName, $layout);

        $result = $render->renderContainer($result, $containerInfo);

        return $result;
    }
}

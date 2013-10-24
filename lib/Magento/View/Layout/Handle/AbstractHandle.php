<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\HandleFactory;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\Layout\Argument\Processor;

abstract class AbstractHandle
{
    /**
     * @var \Magento\View\Layout\HandleFactory
     */
    protected $handleFactory;

    /**
     * @var \Magento\View\Render\RenderFactory
     */
    protected $renderFactory;

    /**
     * @var Processor
     */
    protected $argumentProcessor;

    /**
     * @var int
     */
    protected $nameIncrement = 0;

    /**
     * @param HandleFactory $handleFactory
     * @param RenderFactory $renderFactory
     * @param Processor $argumentProcessor
     */
    public function __construct(
        HandleFactory $handleFactory,
        RenderFactory $renderFactory,
        Processor $argumentProcessor
    ) {
        $this->handleFactory = $handleFactory;
        $this->renderFactory = $renderFactory;
        $this->argumentProcessor = $argumentProcessor;
    }

    /**
     * Parse layout handle node  XML attributes
     *
     * @param Element $layoutElement
     * @return array
     */
    protected function parseAttributes(Element $layoutElement)
    {
        $attributes = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $attributes[$attributeName] = (string)$attribute;
            }
        }
        return $attributes;
    }

    /**
     * Parse layout handle node children of "argument" type
     *
     * @param Element $layoutElement
     * @return array
     */
    protected function parseArguments(Element $layoutElement)
    {
        $arguments = array();
        foreach ($layoutElement->xpath('argument') as $argument) {
            /** @var $argument Element */
            $argumentName = (string)$argument['name'];
            $arguments[$argumentName] = $this->argumentProcessor->parse($argument);
        }

        $arguments = $this->processArguments($arguments);

        return $arguments;
    }

    /**
     * Process arguments
     *
     * @param array $arguments
     * @return array
     */
    protected function processArguments(array $arguments)
    {
        $result = array();
        foreach ($arguments as $name => $argument) {
            $result[$name] = $this->argumentProcessor->process($argument);
        }
        return $result;
    }

    protected function parseChildren(Element $layoutElement, LayoutInterface $layout, $elementName)
    {
        // parse children
        if ($layoutElement->hasChildren()) {
            foreach ($layoutElement as $childXml) {
                /** @var $childXml Element */
                $type = $childXml->getName();
                /** @var $handle \Magento\View\Layout\HandleInterface */
                $handle = $this->handleFactory->get($type);
                $handle->parse($childXml, $layout, $elementName);
            }
        }
    }

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param $parentName
     * @return $this
     */
    protected function parseReference(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $originalParentName = $layoutElement->getAttribute('name');
        $parentName = isset($originalParentName) ? $originalParentName : $parentName;

        $element = $this->parseAttributes($layoutElement);

        $layout->updateElement($parentName, $element);

        // parse children
        $this->parseChildren($layoutElement, $layout, $parentName);

        return $this;
    }

    protected function assignToParentElement(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($parentName)) {
            $alias = !empty($element['as']) ? $element['as'] : $element['name'];
            $layout->setChild($parentName, $element['name'], $alias);
            list($siblingName, $isAfter) = $this->beforeAfterToSibling($element);
            $layout->reorderChild($parentName, $element['name'], $siblingName, $isAfter);
        }
    }

    protected function registerChildren($elementName, LayoutInterface $layout)
    {
        foreach ($layout->getChildNames($elementName) as $childName) {
            $child = $layout->getElement($childName);
            /** @var $handle RenderInterface */
            $handle = $this->handleFactory->get($child['type']);
            $handle->register($child, $layout, $elementName);
        }
    }

    protected function renderChildren($elementName, LayoutInterface $layout, $type)
    {
        $result = '';
        foreach ($layout->getChildNames($elementName) as $childName) {
            $child = $layout->getElement($childName);
            /** @var $handle RenderInterface */
            $handle = $this->handleFactory->get($child['type']);
            if ($handle instanceof RenderInterface) {
                $result .= $handle->render($child, $layout, $elementName, $type);
            }
        }

        return $result;
    }

    /**
     * Analyze "before" and "after" information in the node and return sibling name and whether "after" or "before"
     *
     * @param array $element
     * @return array
     */
    protected function beforeAfterToSibling($element)
    {
        $result = array(null, true);
        if (isset($element['after'])) {
            $result[0] = (string)$element['after'];
        } elseif (isset($element['before'])) {
            $result[0] = (string)$element['before'];
            $result[1] = false;
        }
        return $result;
    }

    protected function getContainerInfo($element)
    {
        $containerInfo = array();

        $containerInfo['label'] = $this->getContainerLabelInfo($element);
        $containerInfo['tag'] = $this->getContainerTagInfo($element);
        $containerInfo['class'] = $this->getContainerClassInfo($element);
        $containerInfo['id'] = $this->getContainerIdInfo($element);

        return $containerInfo;
    }

    protected function getContainerLabelInfo($element)
    {
        return !empty($element['label']) ? $element['label'] : null;
    }

    protected function getContainerTagInfo($element)
    {
        return !empty($element['htmlTag']) ? $element['htmlTag'] : null;
    }

    protected function getContainerClassInfo($element)
    {
        return !empty($element['htmlClass']) ? $element['htmlClass'] : null;
    }

    protected function getContainerIdInfo($element)
    {
        return !empty($element['htmlId']) ? $element['htmlId'] : null;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return AbstractHandle
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        return $this;
    }
}

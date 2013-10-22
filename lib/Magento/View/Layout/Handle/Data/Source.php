<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Data;

use Magento\View\Layout;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\HandleInterface;
use Magento\View\Layout\Handle\DataInterface;
use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\HandleFactory;

class Source implements DataInterface
{
    /**
     * Container type
     */
    const TYPE = 'data';

    /**
     * @var HandleFactory
     */
    protected $handleFactory;

    /**
     * @param HandleFactory $handleFactory
     */
    public function __construct(HandleFactory $handleFactory)
    {
        $this->handleFactory = $handleFactory;
    }

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Source
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }

        $element['type'] = self::TYPE;
        $elementName = $element['name'];

        $layout->addElement($elementName, $element);

        $alias = isset($node['as']) ? $node['as'] : $elementName;
        if (isset($alias) && $parentName) {
            $layout->setChild($parentName, $elementName, $alias);
        }

        // parse children
        if ($layoutElement->hasChildren()) {
            foreach ($layoutElement as $childXml) {
                /** @var $childXml Element */
                $type = $childXml->getName();
                /** @var $handle HandleInterface */
                $handle = $this->handleFactory->get($type);
                $handle->parse($childXml, $layout, $elementName);
            }
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @throws \Exception
     * @return Source
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['class'])) {
            if (!class_exists($element['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $element['class']));
            }

            $elementName = isset($element['name']) ? $element['name'] : null;
            $alias = isset($element['as']) ? $element['as'] : $elementName;

            $layout->addDataSource($element['class'], $elementName, $parentName, $alias);
        }

        return $this;
    }
}

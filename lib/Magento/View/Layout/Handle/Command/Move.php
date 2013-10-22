<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Command;
use Magento\View\Layout\Handle\Render;

class Move implements Command
{
    /**
     * Container type
     */
    const TYPE = 'move';

    /**
     * @var int
     */
    private $inc = 0;

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return $this
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
        $elementName = isset($element['name']) ? $element['name'] : ('Command-Move-' . $this->inc++);

        $layout->addElement($elementName, $element);

        if (isset($parentName)) {
            $layout->setChild($parentName, $elementName, $elementName);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Move
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        $elementName = isset($element['element']) ? $element['element'] : null;
        if (isset($elementName) && isset($parentName)) {
            if ($layout->getElement($element['element'])) {
                $elementParentName = $layout->getParentName($elementName);
                $layout->unsetChild($elementParentName, $elementName);

                if (isset($element['destination'])) {
                    $alias = isset($element['as']) ? $element['as'] : $elementName;
                    $layout->setChild($element['destination'], $elementName, $alias);

                    list($siblingName, $isAfter) = $this->beforeAfterToSibling($element);

                    $layout->reorderChild($element['destination'], $elementName, $siblingName, $isAfter);
                }
            }
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
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
}

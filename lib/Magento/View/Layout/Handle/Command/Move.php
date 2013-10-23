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
use Magento\View\Layout\Handle\CommandInterface;
use Magento\View\Layout\Handle\Render;

class Move extends Handle\AbstractHandle implements CommandInterface
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
     * @return Move
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = $this->parseAttributes($layoutElement);

        $elementName = isset($element['name']) ? $element['name'] : ('Command-Move-' . $this->inc++);
        $element['type'] = self::TYPE;
        $element['name'] = $elementName;

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
                    $toMove = array(
                        'name' => $elementName,
                        'as' => isset($element['as']) ? $element['as'] : null,
                        'before' => isset($element['before']) ? $element['before'] : null,
                        'after' => isset($element['after']) ? $element['after'] : null,
                    );

                    // assign to parent element
                    $this->assignToParentElement($toMove, $layout, $element['destination']);
                }
            }
        }

        $layout->unsetElement($element['name']);

        return $this;
    }
}

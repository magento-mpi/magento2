<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Reference;

use Magento\View\Layout\Handle\Render\Container as OriginalContainer;
use Magento\View\Layout;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;

class Container extends OriginalContainer
{
    /**
     * Container type
     */
    const TYPE = 'referenceContainer';

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Container
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $originalParentName = $layoutElement->getAttribute('name');
        if (isset($originalParentName)) {
            if ($layoutElement->hasChildren()) {
                foreach ($layoutElement as $childXml) {
                    /** @var $childXml Element */
                    $type = $childXml->getName();
                    /** @var $handle Handle */
                    $handle = $this->handleFactory->get($type);
                    $handle->parse($childXml, $layout, $originalParentName);
                }
            }
        }
    }
}

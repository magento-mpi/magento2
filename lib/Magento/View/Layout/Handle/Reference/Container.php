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
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array())
    {
        $name = $layoutElement->getAttribute('name');
        if (isset($name)) {
            $originalBlock = & $layout->getElement($name);
            if ($originalBlock) {
                foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                    if ($attribute) {
                        $originalBlock[$attributeName] = (string)$attribute;
                    }
                }

                // parse children
                if ($layoutElement->hasChildren()) {
                    foreach ($layoutElement as $childXml) {
                        /** @var $childXml Element */
                        $type = $childXml->getName();
                        /** @var $handle Handle */
                        $handle = $this->handleFactory->get($type);
                        $handle->parse($childXml, $layout, $originalBlock);
                    }
                }
            }
        }
    }
}

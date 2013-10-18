<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Reference;

use Magento\View\Layout\Handle\Render\Block as OriginalBlock;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;

class Block extends OriginalBlock
{
    /**
     * Container type
     */
    const TYPE = 'referenceBlock';

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     * @return Handle\Render
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array())
    {
        $name = $layoutElement->getAttribute('name');
        if (isset($name)) {
            $originalBlock = & $layout->getElement($name);
            if (isset($originalBlock)) {
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

        return $this;
    }
}

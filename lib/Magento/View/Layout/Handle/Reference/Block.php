<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Reference;

use Magento\View\Layout\Handle\Render\Block as OriginalBlock;
use Magento\View\LayoutInterface;
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
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Block
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $originalParentName = $layoutElement->getAttribute('name');
        if (isset($originalParentName)) {
            $arguments = array();
            foreach ($layoutElement->attributes() as $attributeName => $attribute) {
                if ($attribute) {
                    $arguments[$attributeName] = (string)$attribute;
                }
            }
            $layout->updateElement($originalParentName, $arguments);

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

        return $this;
    }
}

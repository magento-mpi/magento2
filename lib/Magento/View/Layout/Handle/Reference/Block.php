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
use Magento\View\Layout\HandleInterface;

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
        $this->parseReference($layoutElement, $layout, $parentName);

        return $this;
    }
}

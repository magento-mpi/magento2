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

/**
 * @package Magento\View
 */
class Block extends OriginalBlock
{
    /**
     * Container type
     */
    const TYPE = 'referenceBlock';

    /**
     * @inheritdoc
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $this->parseReference($layoutElement, $layout, $parentName);

        return $this;
    }
}

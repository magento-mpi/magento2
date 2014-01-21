<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

class RendererList extends AbstractBlock
{
    /**
     * Get render by type
     *
     * @param string $type
     * @return bool|AbstractBlock
     */
    public function getRenderer($type)
    {
        return $this->getChildBlock($type);
    }
} 

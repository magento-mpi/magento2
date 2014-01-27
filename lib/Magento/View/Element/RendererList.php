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
     * Retrieve renderer by code
     *
     * @param string $type
     * @param string $defalut
     * @param string $rendererTemplate
     * @return bool|AbstractBlock
     * @throws \RuntimeException
     */
    public function getRenderer($type, $defalut = null, $rendererTemplate = null)
    {
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock($defalut);
        if (!$renderer instanceof BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setRenderedBlock($this);
        if ($rendererTemplate) {
            $renderer->setTemplate($rendererTemplate);
        }
        return $renderer;
    }
} 

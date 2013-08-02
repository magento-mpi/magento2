<?php
/**
 * InlineTool Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_InlineTool extends Magento_Phrase_AbstractRenderer
{
    /**
     * Renderer construct
     *
     * @param Magento_Phrase_RendererInterface|null $renderer
     */
    public function __construct(Magento_Phrase_RendererInterface $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    protected function _render($text, $arguments = array())
    {
        return $text;
    }
}

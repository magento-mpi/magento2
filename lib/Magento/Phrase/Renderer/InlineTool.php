<?php
/**
 * InlineTool Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_InlineTool implements Magento_Phrase_RendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments = array())
    {
        return $text;
    }
}

<?php
/**
 * Placeholder Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Placeholder implements Magento_Phrase_RendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments)
    {
        return $text;
    }
}

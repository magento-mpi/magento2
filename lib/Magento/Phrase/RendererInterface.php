<?php
/**
 * Phrase renderer interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Phrase_RendererInterface
{
    /**
     * Render result text
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    public function render($text, array $arguments);
}

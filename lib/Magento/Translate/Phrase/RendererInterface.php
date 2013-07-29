<?php
/**
 * Phrase renderer interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Translate_Phrase_RendererInterface
{
    /**
     * Render Phrase
     *
     * @param Magento_Translate_Phrase $phrase
     * @return string
     */
    public function render(Magento_Translate_Phrase $phrase);
}

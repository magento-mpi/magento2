<?php
/**
 * Customize Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Customize extends Magento_Phrase_AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function _render($text, $arguments = array())
    {
        return $text;
    }
}

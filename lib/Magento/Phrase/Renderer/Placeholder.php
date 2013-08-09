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
        if ($arguments) {
            $placeholders = array();
            for ($i = 1, $size = count($arguments); $i <= $size; $i++) {
                $placeholders[] = "%$i";
            }
            $text = str_replace($placeholders, $arguments, $text);
        }
        return $text;
    }
}

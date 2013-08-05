<?php
/**
 * Phrase renderer phrase with replacing of placeholders
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Placeholder implements Magento_Phrase_RendererInterface
{

    /**
     * Render result text
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    public function render($text, array $arguments = array())
    {
        $result = $text;
        if ((strpos($text, '%s') !== false || strpos($text, '$s') !== false) !== false && count($arguments) != 0) {
            $result = call_user_func_array('sprintf', array_merge(array($text), $arguments));
        }

        return $result;
    }
}
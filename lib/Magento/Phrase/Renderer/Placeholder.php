<?php
/**
 * Placeholder Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class Placeholder implements \Magento\Phrase\RendererInterface
{
    /**
     * Render source text
     *
     * @param [] $source
     * @param [] $arguments
     * @return string
     */
    public function render(array $source, array $arguments)
    {
        $text = end($source);

        if ($arguments) {
            $placeholders = [];
            foreach (array_keys($arguments) as $key) {
                $placeholders[] = "%" . (is_int($key) ? strval($key + 1) : $key);
            }
            $text = str_replace($placeholders, $arguments, $text);
        }

        return $text;
    }
}

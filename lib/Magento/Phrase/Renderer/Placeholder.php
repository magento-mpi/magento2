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
     * {@inheritdoc}
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    public function render($text, array $arguments)
    {
        if ($arguments) {
            $placeholders = array();
            foreach (array_keys($arguments) as $key) {
                $placeholders[] = "%" . (is_int($key) ? strval($key + 1) : $key);
            }
            $text = str_replace($placeholders, $arguments, $text);
        }
        return $text;
    }
}

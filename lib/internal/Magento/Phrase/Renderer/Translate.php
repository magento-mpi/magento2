<?php
/**
 * Translate Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class Translate implements \Magento\Phrase\RendererInterface
{
    /**
     * @var \Magento\TranslateInterface
     */
    protected $translator;

    /**
     * Renderer construct
     *
     * @param \Magento\TranslateInterface $translator
     */
    public function __construct(\Magento\TranslateInterface $translator)
    {
        $this->translator = $translator;
    }

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

        $code = $this->translator->getTheme() . \Magento\Translate::SCOPE_SEPARATOR . $text;

        $data = $this->translator->getData();

        if (array_key_exists($code, $data)) {
            return $data[$code];
        }
        if (array_key_exists($text, $data)) {
            return $data[$text];
        }

        return $text;
    }
}

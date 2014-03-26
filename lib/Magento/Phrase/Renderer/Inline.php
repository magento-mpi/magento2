<?php
/**
 * Translate Inline Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class Inline implements \Magento\Phrase\RendererInterface
{
    /**
     * @var \Magento\TranslateInterface
     */
    protected $translator;

    /**
     * @var \Magento\Translate\Inline\ProviderInterface
     */
    protected $inlineProvider;

    /**
     * @param \Magento\TranslateInterface $translator
     * @param \Magento\Translate\Inline\ProviderInterface $inlineProvider
     */
    public function __construct(
        \Magento\TranslateInterface $translator,
        \Magento\Translate\Inline\ProviderInterface $inlineProvider
    ) {
        $this->translator = $translator;
        $this->inlineProvider = $inlineProvider;
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

        if (!$this->inlineProvider->get()->isAllowed()) {
            return $text;
        }

        if (strpos($text, '{{{') === false
            || strpos($text, '}}}') === false
            || strpos($text, '}}{{') === false
        ) {
            $text = '{{{'
                . implode('}}{{', array_reverse($source))
                . '}}{{' . $this->translator->getTheme() . '}}}';
        }

        return $text;
    }
}

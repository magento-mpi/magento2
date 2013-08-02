<?php
/**
 * Translate Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Translate extends Magento_Phrase_AbstractRenderer
{
    /**
     * Basic object for translation
     *
     * @var Magento_Translate_TranslateInterface
     */
    protected $_translator;

    /**
     * Renderer construct
     *
     * @param Magento_Translate_TranslateInterface $translator
     * @param Magento_Phrase_RendererInterface $renderer
     */
    public function __construct(
        Magento_Translate_TranslateInterface $translator,
        Magento_Phrase_RendererInterface $renderer
    ) {
        $this->_translator = $translator;
        $this->_renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    protected function _render($text, $arguments = array())
    {
        array_unshift($arguments, $text);

        return call_user_func(array($this->_translator, 'translate'), $arguments);
    }
}

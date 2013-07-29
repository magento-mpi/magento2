<?php
/**
 * Phrase renderer translate
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Translate_Phrase_Renderer_Translate implements Magento_Translate_Phrase_RendererInterface
{
    /**
     * Translator
     *
     * @var Magento_Translate_TranslateInterface
     */
    protected $_translator;

    /**
     * Renderer translate construct
     *
     * @param Magento_Translate_TranslateInterface $translator
     */
    public function __construct(Magento_Translate_TranslateInterface $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * @inheritdoc}
     */
    public function render(Magento_Translate_Phrase $phrase)
    {
        $arguments = $phrase->getArguments();
        array_unshift($arguments, $phrase->getText());

        return call_user_func_array(array($this->_translator, 'translate'), $arguments);
    }
}

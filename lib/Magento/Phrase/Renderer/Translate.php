<?php
/**
 * Phrase renderer translate
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Translate implements Magento_Phrase_RendererInterface
{
    /**
     * Basic object for translation
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
     * {@inheritdoc}
     */
    public function render($text, array $arguments = array())
    {
        array_unshift($arguments, $text);

        return call_user_func_array(array($this->_translator, 'translate'), $arguments);
    }
}

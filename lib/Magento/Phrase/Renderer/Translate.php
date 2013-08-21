<?php
/**
 * Translate Phrase renderer
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
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Renderer construct
     *
     * @param Magento_Core_Model_Translate $translator
     */
    public function __construct(Magento_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments)
    {
        array_unshift($arguments, $text);

        return call_user_func(array($this->_translator, 'translate'), $arguments);
    }
}

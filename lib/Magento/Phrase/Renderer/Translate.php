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
     * Basic object for translation
     *
     * @var \Magento\Phrase\TranslateInterface
     */
    protected $_translator;

    /**
     * Renderer construct
     *
     * @param \Magento\Phrase\TranslateInterface $translator
     */
    public function __construct(\Magento\Phrase\TranslateInterface $translator)
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

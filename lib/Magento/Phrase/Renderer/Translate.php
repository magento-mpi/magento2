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
     * @var \Magento\TranslateInterface
     */
    protected $_translator;

    /**
     * Renderer construct
     *
     * @param \Magento\TranslateInterface $translator
     */
    public function __construct(\Magento\TranslateInterface $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    public function render($text, array $arguments)
    {
        array_unshift($arguments, $text);

        return call_user_func(array($this->_translator, 'translate'), $arguments);
    }
}

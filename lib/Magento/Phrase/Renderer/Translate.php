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
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * Renderer construct
     *
     * @param \Magento\Core\Model\Translate $translator
     */
    public function __construct(\Magento\Core\Model\Translate $translator)
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

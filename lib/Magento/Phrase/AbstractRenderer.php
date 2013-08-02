<?php
/**
 * Abstract Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Phrase_AbstractRenderer implements Magento_Phrase_RendererInterface
{
    /**
     * @var Magento_Phrase_RendererInterface
     */
    protected $_renderer;

    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments = array())
    {
        if (null !== $this->_renderer) {
            $text = $this->_renderer->render($text, $arguments);
        }

        return $this->_render($text, $arguments);
    }

    /**
     * Render result text. Template method
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    protected abstract function _render($text, $arguments = array());
}

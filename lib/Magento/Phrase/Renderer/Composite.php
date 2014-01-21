<?php
/**
 * Composite Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

use \Magento\Phrase\Renderer\Factory;

class Composite implements \Magento\Phrase\RendererInterface
{
    /**
     * Renderer factory
     *
     * @var Factory
     */
    protected $_rendererFactory;

    /**
     * List of \Magento\Phrase\RendererInterface
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Renderer construct
     *
     * @param Factory $rendererFactory
     * @param array $renderers
     */
    public function __construct(
        Factory $rendererFactory,
        array $renderers = array()
    ) {
        $this->_rendererFactory = $rendererFactory;

        foreach ($renderers as $render) {
            $this->_append($render);
        }
    }

    /**
     * Add renderer to the end of the chain
     *
     * @param string $render
     * @return void
     */
    protected function _append($render)
    {
        $this->_renderers[] = $this->_rendererFactory->create($render);
    }

    /**
     * Render result text
     *
     * @param string $text
     * @param array $arguments
     * @return string
     */
    public function render($text, array $arguments = array())
    {
        /** @var \Magento\Phrase\Renderer\Composite $render */
        foreach ($this->_renderers as $render) {
            $text = $render->render($text, $arguments);
        }
        return $text;
    }
}

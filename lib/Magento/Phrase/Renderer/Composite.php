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

class Composite implements \Magento\Phrase\RendererInterface
{
    /**
     * Renderer factory
     *
     * @var \Magento\Phrase\Renderer\Factory
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
     * @param \Magento\Phrase\Renderer\Factory $rendererFactory
     * @param array $renderers
     */
    public function __construct(
        \Magento\Phrase\Renderer\Factory $rendererFactory,
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
     */
    protected function _append($render)
    {
        array_push($this->_renderers, $this->_rendererFactory->create($render));
    }

    /**
     * {@inheritdoc}
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

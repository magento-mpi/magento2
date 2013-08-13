<?php
/**
 * Composite Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Composite implements Magento_Phrase_RendererInterface
{
    /**
     * Renderer factory
     *
     * @var Magento_Phrase_Renderer_Factory
     */
    protected $rendererFactory;

    /**
     * List of Magento_Phrase_RendererInterface
     *
     * @var array
     */
    protected $renderers = array();

    /**
     * Renderer construct
     *
     * @param Magento_Phrase_Renderer_Factory $rendererFactory
     * @param array $renderers
     */
    public function __construct(
        Magento_Phrase_Renderer_Factory $rendererFactory,
        array $renderers = array()
    ) {
        $this->rendererFactory = $rendererFactory;

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
        array_push($this->renderers, $this->rendererFactory->create($render));
    }

    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments = array())
    {
        /** @var Magento_Phrase_Renderer_Composite $render */
        foreach ($this->renderers as $render) {
            $text = $render->render($text, $arguments);
        }
        return $text;
    }
}

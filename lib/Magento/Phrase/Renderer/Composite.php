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

use Magento\Phrase\RendererInterface;

class Composite implements RendererInterface
{
    /**
     * @var RendererInterface[]
     */
    protected $_renderers;

    /**
     * @param RendererInterface[] $renderers
     * @throws \InvalidArgumentException
     */
    public function __construct(array $renderers) {
        foreach ($renderers as $renderer) {
            if (!($renderer instanceof RendererInterface)) {
                throw new \InvalidArgumentException(sprintf(
                    'Instance of the phrase renderer is expected, got %s instead.', get_class($renderer)
                ));
            }
        }
        $this->_renderers = $renderers;
    }

    /**
     * Render source text
     *
     * @param [] $source
     * @param [] $arguments
     * @return string
     */
    public function render(array $source, array $arguments = array())
    {
        $result = $source;
        foreach ($this->_renderers as $render) {
            $result[] = $render->render($result, $arguments);
        }
        return end($result);
    }
}

<?php
/**
 * Phrase (for replacing Data Value with Object)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class Phrase
{
    /**
     * Default phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var \Magento\Phrase\RendererInterface
     */
    private static $_renderer;

    /**
     * String for rendering
     *
     * @var string
     */
    private $_text;

    /**
     * Arguments for placeholder values
     *
     * @var array
     */
    private $_arguments;

    /**
     * Set default Phrase renderer
     *
     * @param \Magento\Phrase\RendererInterface $renderer
     */
    public static function setRenderer(\Magento\Phrase\RendererInterface $renderer)
    {
        self::$_renderer = $renderer;
    }

    /**
     * Phrase construct
     *
     * @param string $text
     * @param array $arguments
     */
    public function __construct($text, array $arguments = array())
    {
        $this->_text = (string)$text;
        $this->_arguments = $arguments;
    }

    /**
     * Render phrase
     *
     * @return string
     */
    public function render()
    {
        return self::$_renderer ? self::$_renderer->render($this->_text, $this->_arguments) : $this->_text;
    }

    /**
     * Defers rendering to the last possible moment (when converted to string)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}

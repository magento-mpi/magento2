<?php
/**
 * Phrase (for replacing Data Value with Object)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase
{
    /**
     * Default phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var Magento_Phrase_RendererInterface
     */
    private static $_defaultRenderer;

    /**
     * Custom phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var Magento_Phrase_RendererInterface
     */
    private $_customRenderer;

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
     * Rendered result
     *
     * @var string
     */
    private $_result;

    /**
     * Set default Phrase renderer
     *
     * @param Magento_Phrase_RendererInterface $defaultRenderer
     * @throws \RuntimeException
     */
    public static function setDefaultRenderer(Magento_Phrase_RendererInterface $defaultRenderer)
    {
        if (null !== self::$_defaultRenderer) {
            throw new RuntimeException('Default renderer is already set');
        }

        self::$_defaultRenderer = $defaultRenderer;
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
     * Set custom Phrase renderer
     *
     * @param Magento_Phrase_RendererInterface $customRenderer
     */
    public function setCustomRenderer(Magento_Phrase_RendererInterface $customRenderer)
    {
        $this->_resetResult();

        $this->_customRenderer = $customRenderer;
    }

    /**
     * Render phrase
     *
     * @return string
     */
    public function render()
    {
        if (null === $this->_result) {
            $this->_result = ($renderer = $this->_getRenderer()) ? $renderer->render($this->_text, $this->_arguments)
                : $this->_text;
        }

        return $this->_result;
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

    /**
     * Reset result of rendering
     */
    protected function _resetResult()
    {
        $this->_result = null;
    }

    /**
     * Get renderer
     *
     * @return bool|Magento_Phrase_RendererInterface
     */
    protected function _getRenderer()
    {
        if (null !== $this->_customRenderer) {
            $renderer = $this->_customRenderer;
        } elseif (self::$_defaultRenderer) {
            $renderer = self::$_defaultRenderer;
        } else {
            $renderer = false;
        }
        return $renderer;
    }
}

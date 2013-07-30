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
     * Phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var Magento_Phrase_RendererInterface
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
     * Rendered result
     *
     * @var string
     */
    private $_result;

    /**
     * Set Phrase renderer
     *
     * @param Magento_Phrase_RendererInterface $renderer
     */
    public static function setRenderer(Magento_Phrase_RendererInterface $renderer)
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
     * Defers rendering to the last possible moment (when converted to string)
     *
     * @return string
     */
    public function __toString()
    {
        if (null === $this->_result) {
            $this->_result = self::$_renderer ? self::$_renderer->render($this->_text, $this->_arguments)
                : $this->_text;
        }

        return $this->_result;
    }
}

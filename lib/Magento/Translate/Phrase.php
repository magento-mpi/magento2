<?php
/**
 * Phrase (for replacing Data Value with Object)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Translate_Phrase
{
    /**
     * Phrase renderer. Allows stacking renderers that "don't know about each other"
     *
     * @var Magento_Translate_Phrase_RendererInterface
     */
    protected static $_renderer;

    /**
     * String for rendering
     *
     * @var string
     */
    protected $_text;

    /**
     * Arguments for placeholder values
     *
     * @var array
     */
    protected $_arguments;

    /**
     * Rendered result
     *
     * @var string
     */
    protected $_result;

    /**
     * Set Phrase renderer
     *
     * @param Magento_Translate_Phrase_RendererInterface $renderer
     */
    public static function setRenderer(Magento_Translate_Phrase_RendererInterface $renderer)
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
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Get arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * Defers rendering to the last possible moment (when converted to string)
     *
     * @return string
     */
    public function __toString()
    {
        if (null === $this->_result) {
            $this->_result = self::$_renderer ? self::$_renderer->render($this) : $this->getText();
        }

        return $this->_result;
    }
}

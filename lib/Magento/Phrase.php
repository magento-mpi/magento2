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
    private static $renderer;

    /**
     * String for rendering
     *
     * @var string
     */
    private $text;

    /**
     * Arguments for placeholder values
     *
     * @var array
     */
    private $arguments;

    /**
     * Set default Phrase renderer
     *
     * @param Magento_Phrase_RendererInterface $renderer
     */
    public static function setRenderer(Magento_Phrase_RendererInterface $renderer)
    {
        self::$renderer = $renderer;
    }

    /**
     * Phrase construct
     *
     * @param string $text
     * @param array $arguments
     */
    public function __construct($text, array $arguments = array())
    {
        $this->text = (string)$text;
        $this->arguments = $arguments;
    }

    /**
     * Render phrase
     *
     * @return string
     */
    public function render()
    {
        return self::$renderer ? self::$renderer->render($this->text, $this->arguments) : $this->text;
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

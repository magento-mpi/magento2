<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;


class ConditionalLineBreak extends LineBreak
{
    protected $alternateBreak;

    public function __construct($alternateBreak)
    {
        $this->alternateBreak = $alternateBreak;
    }

    /**
     * This method returns the current value of the break.
     * @return string
     */
    public function __toString()
    {
        return $this->alternate ? (string) $this->alternateBreak : HardLineBreak::EOL;
    }

    /**
     * This member returns the level which the line break represents. It is up to the break itself to return an
     * appropriate value with respect to other breaks in the line.
     * @return int
     */
    public function getLevel()
    {
        return 1;
    }

    /**
     * This method returns if the next line should be indented.
     */
    public function isNextLineIndented()
    {
        return true;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class HardLineBreak extends LineBreak
{
    const EOL = "\n";

    /**
     * This method translates this instance to a string.
     * @return string
     */
    public function __toString()
    {
        return self::EOL;
    }

    /**
     * This member returns the level which the line break represents. It is up to the break itself to return an
     * appropriate value with respect to other breaks in the line.
     * @return int
     */
    public function getLevel()
    {
        return 0;
    }

    /**
     * This method returns if the next line should be indented.
     */
    public function isNextLineIndented()
    {
        return false;
    }
}

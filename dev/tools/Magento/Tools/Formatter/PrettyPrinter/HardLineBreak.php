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
     * This method returns the value for the break based on the passed in information.
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     */
    public function getValue($level, $index, $total)
    {
        // always return the same value since this always represents a LF
        return self::EOL;
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     * @return mixed
     */
    public function getSortOrder()
    {
        // hard breaks should always be processed
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

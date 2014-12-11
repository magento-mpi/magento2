<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @param array &$lineBreakData Data that the line break can use.
     * @return string
     */
    public function getValue($level, $index, $total, array &$lineBreakData)
    {
        // always return the same value since this always represents a LF
        return self::EOL;
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        // hard breaks should always be processed
        return 0;
    }

    /**
     * This method returns if the next line should be indented.
     *
     * @return bool
     */
    public function isNextLineIndented()
    {
        return false;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

abstract class LineBreak
{
    /**
     * This method returns the value for the break based on the passed in information.
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     */
    abstract public function getValue($level, $index, $total);

    /**
     * This method returns if this class of line breaks are grouped by class. If not grouped by
     * class, it is assumed to be grouped by instance.
     * @return bool
     */
    public function isGroupedByClass()
    {
        return true;
    }

    /**
     * This method returns if the next line should be indented.
     */
    abstract public function isNextLineIndented();
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

abstract class LineBreak
{
    /**
     * This method returns the value for the break based on the passed in information.
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @param array &$lineBreakData Data that the line break can use.
     * @return HardIndentLineBreak|HardLineBreak|string|false
     */
    abstract public function getValue($level, $index, $total, array &$lineBreakData);

    /**
     * This method returns a flag indicating that when placed in a list, an additional instance is
     * required after the list.
     *
     * @return bool
     */
    public function isAfterListRequired()
    {
        return false;
    }

    /**
     * This method returns if the next line should be indented.
     *
     * @return bool
     */
    abstract public function isNextLineIndented();

    /**
     * This method returns an id used to group line breaks occurring in the same line together.
     * This is typically either the class name or the instance id.
     *
     * @return string
     */
    public function getGroupingId()
    {
        return get_class($this);
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    abstract public function getSortOrder();
}

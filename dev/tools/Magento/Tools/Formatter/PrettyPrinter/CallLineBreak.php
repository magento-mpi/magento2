<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class CallLineBreak
 * @package Magento\Tools\Formatter\PrettyPrinter
 *
 * array()
 *
 * array(x1,x2,x3x)
 * array(x
 * 1,x
 * 2,x
 * 3x
 * )
 *
 * 1 blank	HardIndent
 * 2 space	HardIndent
 * 3 space	HardIndent
 * 4 blank  Hard
 */
class CallLineBreak extends ConditionalLineBreak
{
    public function __construct()
    {
        parent::__construct(array());
    }

    /**
     * This method returns the value for the break based on the passed in information.
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     */
    public function getValue($level, $index, $total)
    {
        switch ($level) {
            case 0:
                switch ($index) {
                    case 0:
                    case $total - 1:
                        $result = '';
                        break;
                    default:
                        $result = ' ';
                        break;
                }
                break;
            default:
                // this class only handle 2 levels, so treat later levels the same as the second
                switch ($index) {
                    case $total - 1:
                        $result = new HardLineBreak();
                        break;
                    default:
                        $result = new HardIndentLineBreak();
                        break;
                }
                break;
        }
        return $result;
    }

    /**
     * This method returns a flag indicating that when placed in a list, an additional instance is
     * required after the list.
     * @return bool
     */
    public function isAfterListRequired()
    {
        return true;
    }

    /**
     * This method returns if this class of line breaks are grouped by class. If not grouped by
     * class, it is assumed to be grouped by instance.
     * @return bool
     */
    public function isGroupedByClass()
    {
        return false;
    }
}
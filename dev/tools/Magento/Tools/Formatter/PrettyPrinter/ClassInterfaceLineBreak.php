<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class ClassInterfaceLineBreak
 *
 * class alpha extends beta implementsxi1,xi2,xi3
 * {
 *
 * class alpha extends beta implementsx
 * i1,x
 * i2,x
 * i3
 * {
 *
 * 1	nothing	\n
 * 2	blank	\n
 * 3	blank	\n
 */
class ClassInterfaceLineBreak extends ConditionalLineBreak
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * This method returns the value for the break based on the passed in information.
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @param array &$lineBreakData Data that the line break can use.
     * @return HardIndentLineBreak|HardLineBreak|string|false
     */
    public function getValue($level, $index, $total, array &$lineBreakData)
    {
        switch ($level) {
            case 0:
                $result = ' ';
                break;
            default:
                // this class only handle 2 levels, so treat later levels the same as the second
                $result = new HardIndentLineBreak();
                break;
        }
        return $result;
    }
}

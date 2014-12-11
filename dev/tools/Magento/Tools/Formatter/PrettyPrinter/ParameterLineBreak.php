<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class ParameterLineBreak
 *
 *    public function alpha(xTestClass $a,xTestClass $b,xTestClass $C,xTestClass $Dx)x
 *    {
 *    }
 *
 *    public function alpha(x
 *    TestClass $a,x
 *    TestClass $b,x
 *    TestClass $C,x
 *    TestClass $Dx
 *    )x{
 *    }
 *
 *    1	nothing	\n
 *    2	space	\n
 *    3	space	\n
 *    4	space	\n
 *    5	nothing	\n
 *    6	\n  	blank
 */
class ParameterLineBreak extends ConditionalLineBreak
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        return 50;
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
        // if there are more than 2 markers, that means there are actually parameters
        if ($total > 2) {
            switch ($level) {
                case 0:
                    switch ($index) {
                        case 0:
                        case $total - 2:
                            $result = '';
                            break;

                        case $total - 1:
                            $result = new HardLineBreak();
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
                            $result = ' ';
                            break;
                        default:
                            if ($index < $total - 2) {
                                $result = new HardIndentLineBreak();
                            } else {
                                $result = new HardLineBreak();
                            }
                            break;
                    }
            }
        } else {
            // otherwise, there are no parameters, so treat all levels the same
            $result = new HardLineBreak();
        }

        return $result;
    }

    /**
     * This method returns a flag indicating that when placed in a list, an additional instance is
     * required after the list.
     *
     * @return bool
     */
    public function isAfterListRequired()
    {
        return true;
    }
}

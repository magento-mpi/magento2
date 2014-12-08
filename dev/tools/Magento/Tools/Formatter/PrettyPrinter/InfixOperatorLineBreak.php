<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Operator\AbstractInfixOperator;

class InfixOperatorLineBreak extends ConditionalLineBreak
{
    /**
     * @var AbstractInfixOperator
     */
    private $operator;

    /**
     * @param AbstractInfixOperator $operator
     */
    public function __construct(AbstractInfixOperator $operator)
    {
        parent::__construct([[' '], [new HardIndentLineBreak(), new HardLineBreak()]]);
        $this->operator = $operator;
    }

    /**
     * This method returns an id used to group line breaks occurring in the same line together.
     * This is typically either the class name or the instance id.
     *
     * @return string
     */
    public function getGroupingId()
    {
        return get_class($this->operator) . 'LineBreak';
    }

    /**
     * @return AbstractInfixOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        return 200 + 99 - $this->operator->precedence();
    }

    /**
     * This method returns the value for the break based on the passed in information.
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @param array &$lineBreakData Data that the line break can use.
     * @return HardLineBreak|HardIndentLineBreak|string
     */
    public function getValue($level, $index, $total, array &$lineBreakData)
    {
        switch ($level) {
            case 0:
                $retval = ' ';
                break;
            default:
                if (isset(
                    $lineBreakData[NodeLevelerSortOrder::INDENT_LEVEL]
                ) && $lineBreakData[NodeLevelerSortOrder::INDENT_LEVEL] > 0
                ) {
                    $retval = new HardLineBreak();
                } else {
                    $retval = new HardIndentLineBreak();
                }
        }
        return $retval;
    }
}

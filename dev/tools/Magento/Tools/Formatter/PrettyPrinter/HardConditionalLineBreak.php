<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class is use to return on hard line break only if it is not followed by specified elements.
 * Class HardConditionalLineBreak
 */
class HardConditionalLineBreak extends HardLineBreak implements LineConditionInterface
{
    /**
     * This member holds the condition string used for validating the hard return.
     *
     * @var LineBreakCondition $condition
     */
    protected $condition;

    /**
     * @param LineBreakCondition $condition
     */
    public function __construct(LineBreakCondition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * This method checks the current condition for the next token being added to the line and
     * determines if the current token should be removed.
     *
     * @param array &$tokens Array of existing tokens
     * @param string $nextToken String containing the next token being added to the line.
     * @return mixed
     */
    public function processToken(array &$tokens, $nextToken)
    {
        return $this->condition->process($tokens, $nextToken);
    }
}

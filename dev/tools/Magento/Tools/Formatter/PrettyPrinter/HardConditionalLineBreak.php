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
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class HardConditionalLineBreak extends HardLineBreak implements LineCondition
{
    /**
     * This member holds the condition string used for validating the hard return.
     * @var string
     */
    protected $condition;

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * This method checks the current condition for the next token being added to the line and
     * determines if the current token should be removed.
     * @param string $nextToken String containing the next token being added to the line.
     */
    public function removeToken($nextToken)
    {
        $removeToken = false;
        if (is_string($nextToken)) {
            $removeToken = strncmp($nextToken, $this->condition, strlen($this->condition)) === 0;
        }
        return $removeToken;
    }
}

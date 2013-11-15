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

    public function __construct(AbstractInfixOperator $operator)
    {
        parent::__construct(array(array(' '), array(new HardIndentLineBreak())));
        $this->operator = $operator;
    }

    /**
     * This method returns an id used to group line breaks occurring in the same line together.
     * This is typically either the class name or the instance id.
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
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        return 200 + 99 - $this->operator->precedence();
    }
}

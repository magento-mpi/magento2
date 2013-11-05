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
     * @return AbstractInfixOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }


    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     * @return mixed
     */
    public function getSortOrder()
    {
        // TODO add inverse precedence to sort order
        return 2;
    }
}

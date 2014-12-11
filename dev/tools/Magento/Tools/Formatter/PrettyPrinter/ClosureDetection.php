<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use PHPParser_Node_Arg;
use PHPParser_Node_Expr_ArrayItem;
use PHPParser_Node_Expr_Closure;
use PHPParser_Node_Expr_FuncCall;
use PHPParser_Node_Expr_MethodCall;

class ClosureDetection
{
    /**
     * This member holds the flag indicating closure has been detected.
     *
     * @var bool
     */
    protected $closure = false;

    /**
     * This method constructs the closure detection based on arguments passed in.
     *
     * @param array $arguments Array of arguments on which to detect closures.
     */
    public function __construct(array $arguments)
    {
        $this->closure = $this->checkArguments($arguments);
    }

    /**
     * This method returns the flag indicating if closure has been detected.
     *
     * @return bool
     */
    public function hasClosure()
    {
        return $this->closure;
    }

    /**
     * This method returns if the list of arguments contain a closure element.
     *
     * @param array $arguments Array of arguments on which to detect closures.
     * @return bool
     */
    protected function checkArguments(array $arguments)
    {
        $closure = false;
        // only need to look if something was specified
        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                if ($this->hasClosureArgument($argument)) {
                    $closure = true;
                    break;
                }
            }
        }
        return $closure;
    }

    /**
     * This method returns if the passed in argument contains a closure reference.
     *
     * @param mixed $argument Argument to check.
     * @return bool
     */
    protected function hasClosureArgument($argument)
    {
        $closure = false;
        if ($argument instanceof PHPParser_Node_Arg && $argument->value instanceof PHPParser_Node_Expr_Closure ||
            $argument instanceof PHPParser_Node_Expr_ArrayItem &&
            $argument->value instanceof PHPParser_Node_Expr_Closure
        ) {
            $closure = true;
        } elseif ($argument instanceof PHPParser_Node_Arg &&
            ($argument->value instanceof PHPParser_Node_Expr_FuncCall ||
            $argument->value instanceof PHPParser_Node_Expr_MethodCall)
        ) {
            $closure = $this->checkArguments($argument->value->args);
        }
        return $closure;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Arguments;

use Magento\Data\Argument\InterpreterInterface;
use Magento\Data\Argument\MissingOptionalValueException;
use Magento\App\Arguments;

/**
 * Interpreter that returns value of an application argument, retrieving its name from a constant
 */
class ArgumentInterpreter implements InterpreterInterface
{
    /**
     * @var Arguments
     */
    private $arguments;

    /**
     * @param Arguments $arguments
     */
    public function __construct(Arguments $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws MissingOptionalValueException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['value']) || !defined($data['value'])) {
            throw new \InvalidArgumentException('Constant name of application argument is expected.');
        }
        $constantName = $data['value'];
        $argumentName = constant($constantName);
        $result = $this->arguments->get($argumentName);
        if ($result === null) {
            throw new MissingOptionalValueException("Value of application argument '$argumentName' is not defined.");
        }
        return $result;
    }
}

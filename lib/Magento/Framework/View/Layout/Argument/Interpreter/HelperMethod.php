<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Argument\Interpreter;

use Magento\ObjectManager;
use Magento\Framework\Data\Argument\InterpreterInterface;

/**
 * Interpreter that returns invocation result of a helper method
 */
class HelperMethod implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var NamedParams
     */
    private $paramsInterpreter;

    /**
     * @param ObjectManager $objectManager
     * @param NamedParams $paramsInterpreter
     */
    public function __construct(ObjectManager $objectManager, NamedParams $paramsInterpreter)
    {
        $this->objectManager = $objectManager;
        $this->paramsInterpreter = $paramsInterpreter;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['helper']) || substr_count($data['helper'], '::') != 1) {
            throw new \InvalidArgumentException('Helper method name in format "\Class\Name::methodName" is expected.');
        }
        $helperMethod = $data['helper'];
        list($helperClass, $methodName) = explode('::', $helperMethod, 2);
        if (!method_exists($helperClass, $methodName)) {
            throw new \InvalidArgumentException("Helper method '{$helperMethod}' does not exist.");
        }
        $methodParams = $this->paramsInterpreter->evaluate($data);
        $methodParams = array_values($methodParams);
        // Use positional argument binding instead of named binding
        $helperInstance = $this->objectManager->get($helperClass);
        return call_user_func_array(array($helperInstance, $methodName), $methodParams);
    }
}

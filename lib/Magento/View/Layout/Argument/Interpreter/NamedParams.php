<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Argument\Interpreter;

use Magento\ObjectManager;
use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter of named parameters
 */
class NamedParams implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Interpreter of individual parameter
     *
     * @var InterpreterInterface
     */
    private $paramInterpreter;

    /**
     * @param ObjectManager $objectManager
     * @param InterpreterInterface $paramInterpreter
     */
    public function __construct(ObjectManager $objectManager, InterpreterInterface $paramInterpreter)
    {
        $this->objectManager = $objectManager;
        $this->paramInterpreter = $paramInterpreter;
    }

    /**
     * {@inheritdoc}
     * @return array
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        $params = isset($data['param']) ? $data['param'] : array();
        if (!is_array($params)) {
            throw new \InvalidArgumentException('Layout argument parameters are expected to be an array.');
        }
        $result = array();
        foreach ($params as $paramKey => $paramData) {
            if (!is_array($paramData)) {
                throw new \InvalidArgumentException('Parameter data of layout argument is expected to be an array.');
            }
            $result[$paramKey] = $this->paramInterpreter->evaluate($paramData);
        }
        return $result;
    }
}

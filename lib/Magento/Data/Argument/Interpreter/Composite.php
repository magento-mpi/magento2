<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

use Magento\ObjectManager;
use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter that aggregates named interpreters and delegates every evaluation to one of them
 */
class Composite implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array Format: array('<name>' => '<class>', ...)
     */
    private $interpreters;

    /**
     * Data key that holds name of an interpreter to be used for that data
     *
     * @var string
     */
    private $discriminator;

    /**
     * @var InterpreterInterface[]
     */
    private $instances = array();

    /**
     * @param ObjectManager $objectManager
     * @param array $interpreters
     * @param $discriminator
     */
    public function __construct(
        ObjectManager $objectManager,
        array $interpreters,
        $discriminator
    ) {
        $this->objectManager = $objectManager;
        $this->interpreters = $interpreters;
        $this->discriminator = $discriminator;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data[$this->discriminator])) {
            throw new \InvalidArgumentException(sprintf(
                'Value for key "%s" is missing in the argument data.', $this->discriminator
            ));
        }
        $interpreterName = $data[$this->discriminator];
        unset($data[$this->discriminator]);
        $interpreter = $this->getInterpreter($interpreterName);
        return $interpreter->evaluate($data);
    }

    /**
     * Retrieve interpreter instance by its unique name
     *
     * @param string $name
     * @return InterpreterInterface
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function getInterpreter($name)
    {
        if (!isset($this->interpreters[$name])) {
            throw new \InvalidArgumentException("No argument parser is defined for value type '$name'.");
        }
        if (!isset($this->instances[$name])) {
            $interpreterClass = $this->interpreters[$name];
            /** @var $result InterpreterInterface */
            $result = $this->objectManager->create($interpreterClass);
            if (!($result instanceof InterpreterInterface)) {
                throw new \UnexpectedValueException(sprintf(
                    'Argument parser instance is expected, got %s instead.', get_class($result)
                ));
            }
            $this->instances[$name] = $result;
        }
        return $this->instances[$name];
    }
}

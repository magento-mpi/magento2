<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Argument\Interpreter;

use Magento\ObjectManager\Config;
use Magento\ObjectManager\Config\Argument\ObjectFactory;
use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter that creates an instance by a type name taking into account whether it's shared or not
 */
class Object implements InterpreterInterface
{
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * {@inheritdoc}
     * @return object
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (empty($data['value'])) {
            throw new \InvalidArgumentException('Object class name is missing.');
        }
        $className = $data['value'];
        $isShared = isset($data['shared']) ? $data['shared'] != 'false' : null;
        $result = $this->objectFactory->create($className, $isShared);
        return $result;
    }
}

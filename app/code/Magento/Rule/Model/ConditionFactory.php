<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rule\Model;

use Magento\Framework\ObjectManager;

class ConditionFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Store all used condition models
     *
     * @var array
     */
    private $conditionModels = [];

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create new object for each requested model.
     * If model is requested first time, store it at array.
     * It's made by performance reasons to avoid initialization of same models each time when rules are being processed.
     *
     * @param string $type
     *
     * @return \Magento\Rule\Model\Condition\ConditionInterface
     *
     * @throws \LogicException
     * @throws \BadMethodCallException
     */
    public function create($type)
    {
        if (!array_key_exists($type, $this->conditionModels)) {
            $this->conditionModels[$type] = $this->objectManager->create($type);
        }

        return clone $this->conditionModels[$type];
    }
}

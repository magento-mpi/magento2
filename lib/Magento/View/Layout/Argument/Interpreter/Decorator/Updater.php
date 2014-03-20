<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout\Argument\Interpreter\Decorator;

use Magento\ObjectManager;
use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter decorator that passes value, computed by subject of decoration, through the sequence of "updaters"
 */
class Updater implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var InterpreterInterface
     */
    private $subject;

    /**
     * @param ObjectManager $objectManager
     * @param InterpreterInterface $subject
     */
    public function __construct(ObjectManager $objectManager, InterpreterInterface $subject)
    {
        $this->objectManager = $objectManager;
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        $updaters = !empty($data['updater']) ? $data['updater'] : array();
        unset($data['updater']);
        if (!is_array($updaters)) {
            throw new \InvalidArgumentException('Layout argument updaters are expected to be an array of classes.');
        }
        $result = $this->subject->evaluate($data);
        foreach ($updaters as $updaterClass) {
            $result = $this->applyUpdater($result, $updaterClass);
        }
        return $result;
    }

    /**
     * Invoke an updater, passing an input value to it, and return invocation result
     *
     * @param mixed $value
     * @param string $updaterClass
     * @return mixed
     * @throws \UnexpectedValueException
     */
    protected function applyUpdater($value, $updaterClass)
    {
        /** @var \Magento\View\Layout\Argument\UpdaterInterface $updaterInstance */
        $updaterInstance = $this->objectManager->get($updaterClass);
        if (!$updaterInstance instanceof \Magento\View\Layout\Argument\UpdaterInterface) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Instance of layout argument updater is expected, got %s instead.',
                    get_class($updaterInstance)
                )
            );
        }
        return $updaterInstance->update($value);
    }
}

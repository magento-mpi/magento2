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
 * Interpreter that retrieves options from an option source model
 */
class Options implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     * @return array Format: array(array('value' => <value>, 'label' => '<label>'), ...)
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['model'])) {
            throw new \InvalidArgumentException('Options source model class is missing.');
        }
        $modelClass = $data['model'];
        $modelInstance = $this->objectManager->get($modelClass);
        if (!($modelInstance instanceof \Magento\Data\OptionSourceInterface)) {
            throw new \UnexpectedValueException(sprintf(
                "Instance of the options source model is expected, got %s instead.", get_class($modelInstance)
            ));
        }
        $result = array();
        foreach ($modelInstance->toOptionArray() as $value => $label) {
            if (is_array($label)) {
                $result[] = $label;
            } else {
                $result[] = array('value' => $value, 'label' => $label);
            }
        }
        return $result;
    }
}

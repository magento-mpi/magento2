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
 * Interpreter of string data type that may optionally perform text translation
 */
class String implements InterpreterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function evaluate(array $data)
    {
        if (isset($data['value'])) {
            $result = $data['value'];
            $needTranslation = !empty($data['translate']);
            if ($needTranslation) {
                $result = __($result);
            }
        } else {
            $result = '';
        }
        return $result;
    }
}

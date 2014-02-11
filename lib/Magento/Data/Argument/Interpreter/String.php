<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter of string data type that may optionally perform text translation
 */
class String implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     * @return string
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (isset($data['value'])) {
            $result = $data['value'];
            if (!is_string($result)) {
                throw new \InvalidArgumentException('String value is expected.');
            }
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

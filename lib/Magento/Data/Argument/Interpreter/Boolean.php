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
 * Interpreter of boolean data type, such as boolean itself or boolean string
 *
 * @link http://www.php.net/manual/en/filter.filters.validate.php Supported boolean string values
 */
class Boolean implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['value'])) {
            throw new \InvalidArgumentException('Boolean value is missing.');
        }
        $result = $data['value'];
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($result === null) {
            throw new \InvalidArgumentException('Value is expected to be boolean or boolean string.');
        }
        return $result;
    }
}

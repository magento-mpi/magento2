<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument. Type string.
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_String extends Magento_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * Process argument value
     *
     * @param array $argument
     * @return string|Magento_Phrase
     * @throws InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        $value = $argument['value'];

        if (!empty($value['translate'])) {
            $value['string'] = __($value['string']);
        }

        return $value['string'];
    }

    /**
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);

        if (!isset($argument['value']['string'])) {
            throw new InvalidArgumentException(
                'Passed value has incorrect format. ' . $this->_getArgumentInfo($argument)
            );
        }

        if (!is_string($argument['value']['string'])) {
            throw new InvalidArgumentException(
                'Value is not string argument. ' . $this->_getArgumentInfo($argument)
            );
        }
    }

    /**
     * Retrieve value from argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array|null
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        $value = parent::_getArgumentValue($argument);
        if (!isset($value)) {
            return null;
        }
        $result = array('string' => $value);
        if ($argument->getAttribute('translate')) {
            $result['translate'] = true;
        }
        return $result;
    }
}

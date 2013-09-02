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
     * Parse argument node
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array
     */
    public function parse(Magento_Core_Model_Layout_Element $argument)
    {
        $result = parent::parse($argument);
        $result = array_merge_recursive($result, array(
            'value' => $this->_getArgumentValue($argument),
        ));
        if ($argument->getAttribute('translate')) {
            $result['translate'] = true;
        }

        return $result;
    }

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

        if (!empty($argument['translate'])) {
            $argument['value'] = __($argument['value']);
        }

        return $argument['value'];
    }

    /**
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate($argument)
    {
        if (!isset($argument['value'])) {
            throw new InvalidArgumentException('Value is required for string argument');
        }

        if (!is_string($argument['value'])) {
            throw new InvalidArgumentException('Value is not string argument');
        }
    }
}

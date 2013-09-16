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
 * Layout object abstract argument
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Core_Model_Layout_Argument_HandlerAbstract
    implements Magento_Core_Model_Layout_Argument_HandlerInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Retrieve value from argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return mixed|null
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        if ($this->_isUpdater($argument)) {
            return null;
        }
        if (isset($argument->value)) {
            $value = $argument->value;
        } else {
            $value = $argument;
        }
        return trim((string)$value);
    }

    /**
     * Check whether updater used and value not overwriten
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return string
     */
    protected function _isUpdater(Magento_Core_Model_Layout_Element $argument)
    {
        $updaters = $argument->xpath('./updater');
        if (!empty($updaters) && !isset($argument->value)) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve xsi:type attribute value from argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return string
     */
    protected function _getArgumentType(Magento_Core_Model_Layout_Element $argument)
    {
        return (string)$argument->attributes('xsi', true)->type;
    }

    /**
     * Parse argument node
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array
     */
    public function parse(Magento_Core_Model_Layout_Element $argument)
    {
        $result = array();
        $updaters = array();
        $result['type'] = $this->_getArgumentType($argument);
        foreach ($argument->xpath('./updater') as $updaterNode) {
            /** @var $updaterNode Magento_Core_Model_Layout_Element */
            $updaters[uniqid() . '_' . mt_rand()] = trim((string)$updaterNode);
        }

        $result = !empty($updaters) ? $result + array('updaters' => $updaters) : $result;
        $argumentValue = $this->_getArgumentValue($argument);
        if (isset($argumentValue)) {
            $result = array_merge_recursive($result, array(
                'value' => $argumentValue
            ));
        }
        return $result;
    }

    /**
     * Validate parsed argument before processing
     *
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        if (!isset($argument['value'])) {
            throw new InvalidArgumentException(
                'Value is required for argument. ' . $this->_getArgumentInfo($argument)
            );
        }
    }

    /**
     * @param array $argument
     * @return string
     */
    protected function _getArgumentInfo($argument)
    {
        return  'Argument: ' . json_encode($argument);
    }
}

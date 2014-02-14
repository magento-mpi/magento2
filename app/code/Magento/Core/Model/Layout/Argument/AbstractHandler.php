<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Layout\Argument;

/**
 * Layout object abstract argument
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class AbstractHandler implements \Magento\View\Layout\Argument\HandlerInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Retrieve value from argument
     *
     * @param \Magento\View\Layout\Element $argument
     * @return string|null
     */
    protected function _getArgumentValue(\Magento\View\Layout\Element $argument)
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
     * Check whether updater used and value not overwritten
     *
     * @param \Magento\View\Layout\Element $argument
     * @return bool
     */
    protected function _isUpdater(\Magento\View\Layout\Element $argument)
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
     * @param \Magento\View\Layout\Element $argument
     * @return string
     */
    protected function _getArgumentType(\Magento\View\Layout\Element $argument)
    {
        return (string)$argument->attributes('xsi', true)->type;
    }

    /**
     * Parse argument node
     * @param \Magento\View\Layout\Element $argument
     * @return array
     */
    public function parse(\Magento\View\Layout\Element $argument)
    {
        $result = array();
        $updaters = array();
        $result['type'] = $this->_getArgumentType($argument);
        foreach ($argument->xpath('./updater') as $updaterNode) {
            /** @var $updaterNode \Magento\View\Layout\Element */
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
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        if (!isset($argument['value'])) {
            throw new \InvalidArgumentException(
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

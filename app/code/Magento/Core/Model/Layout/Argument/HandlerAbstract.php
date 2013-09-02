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
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }


    /**
     * Retrieve value from argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return mixed
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        if (isset($argument->value)) {
            $value = $argument->value;
        } else {
            $value = $argument;
        }
        return trim((string)$value);
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
            $updaters[] = trim((string)$updaterNode);
        }
        return !empty($updaters) ? $result + array('updater' => $updaters) : $result;
    }
}

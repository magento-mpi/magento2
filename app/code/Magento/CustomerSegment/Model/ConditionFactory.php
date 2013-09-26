<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Conditions factory
 */
class Magento_CustomerSegment_Model_ConditionFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new condition object
     *
     * @param string $className
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Rule_Model_Condition_Interface
     */
    public function create($className, array $data = array())
    {
        $classNamePrefix = 'Magento_CustomerSegment_Model_Segment_Condition_';
        if (false === strpos($className, $classNamePrefix)) {
            $className = $classNamePrefix . $className;
        }
        $condition = $this->_objectManager->create($className, $data);
        if (false == ($condition instanceof Magento_Rule_Model_Condition_Abstract)) {
            throw new InvalidArgumentException($className . ' doesn\'t extends Magento_Rule_Model_Condition_Abstract');
        }
        return $condition;
    }
}

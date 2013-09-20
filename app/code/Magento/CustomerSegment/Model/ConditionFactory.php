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
 * Indexer factory
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
     * Create new indexer object
     *
     * @param string $conditionClassName
     * @param array $data
     * @return Magento_Rule_Model_Condition_Interface
     * @throws InvalidArgumentException
     */
    public function create($conditionClassName = '', array $data = array())
    {
        $condition = $this->_objectManager->create($conditionClassName, $data);
        if (false == ($condition instanceof Magento_Rule_Model_Condition_Interface)) {
            throw new InvalidArgumentException($conditionClassName
                . ' doesn\'t implement Magento_Rule_Model_Condition_Interface'
            );
        }
        return $condition;
    }
}

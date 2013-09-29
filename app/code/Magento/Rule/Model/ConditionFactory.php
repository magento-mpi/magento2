<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rule\Model;

class ConditionFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new action object
     *
     * @param string $type
     * @param array $data
     * @return \Magento\Rule\Model\Condition\ConditionInterface
     */
    public function create($type, array $data = array())
    {
        return $this->_objectManager->create($type, $data);
    }
}

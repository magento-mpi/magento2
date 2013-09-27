<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for Credit Card types
 */
class Magento_Centinel_Model_StateFactory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * State class map
     *
     * @var array
     */
    protected $_stateClassMap;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $stateClassMap - key stands for card type, value define the validator class
     */
    public function __construct(Magento_ObjectManager $objectManager, array $stateClassMap = array())
    {
        $this->_objectManager = $objectManager;
        $this->_stateClassMap = $stateClassMap;
    }

    /**
     * Create state object
     *
     * @param string $cardType
     * @return Magento_Centinel_Model_StateAbstract|false
     */
    public function createState($cardType)
    {
        if (!isset($this->_stateClassMap[$cardType])) {
            return false;
        }
        return $this->_objectManager->create($this->_stateClassMap[$cardType]);
    }
}

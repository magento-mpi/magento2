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
     * Config
     *
     * @var Magento_Centinel_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Centinel_Model_Config $config
     */
    public function __construct(Magento_ObjectManager $objectManager, Magento_Centinel_Model_Config $config)
    {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Create state object
     *
     * @param string $cardType
     * @return Magento_Centinel_Model_StateAbstract|bool
     */
    public function createState($cardType)
    {
        $stateClass = $this->_config->getStateModelClass($cardType);
        return $stateClass ? $this->_objectManager->create($stateClass) : false;
    }
}

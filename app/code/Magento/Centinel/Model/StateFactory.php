<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Model;

/**
 * Factory class for Credit Card types
 */
class StateFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Config
     *
     * @var \Magento\Centinel\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Centinel\Model\Config $config
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Centinel\Model\Config $config)
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

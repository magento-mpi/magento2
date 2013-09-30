<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for \Magento_Paypal_Model_Express_Checkout
 */
class Magento_Paypal_Model_Express_Checkout_Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(\Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return \Magento_Paypal_Model_Express_Checkout
     */
    public function create($className, array $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}

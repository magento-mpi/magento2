<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Express\Checkout;

/**
 * Factory class for \Magento\Paypal\Model\Express\Checkout
 */
class Factory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Paypal\Model\Express\Checkout
     */
    public function create($className, array $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}

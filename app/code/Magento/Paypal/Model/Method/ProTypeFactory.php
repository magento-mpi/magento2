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
 * Factory class for payment method model
 */
namespace Magento\Paypal\Model\Method;

class ProTypeFactory
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
     * @return object
     */
    public function create($className, array $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}

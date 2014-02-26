<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model;

use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;

/**
 * Factory class for \Magento\Shipping\Model\Carrier\AbstractCarrierOnline
 */
class CarrierFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

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
     * @return \Magento\Shipping\Model\Carrier\AbstractCarrierOnline
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $carrier =  $this->_objectManager->create($className, $data);
        if (!$carrier instanceof AbstractCarrierOnline) {
            throw new \InvalidArgumentException(
                "{$className} don't extend \Magento\Shipping\Model\Carrier\AbstractCarrierOnline"
            );
        }
        return $carrier;
    }
}

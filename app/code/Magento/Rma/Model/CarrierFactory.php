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

/**
 * Factory class for \Magento\Usa\Model\Shipping\Carrier\AbstractCarrier
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
     * @return \Magento\Usa\Model\Shipping\Carrier\AbstractCarrier
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $carrier =  $this->_objectManager->create($className, $data);
        if ($carrier instanceof \Magento\Usa\Model\Shipping\Carrier\AbstractCarrier) {
            throw new \InvalidArgumentException("{$className} don't extend \Magento\Usa\Model\Shipping\Carrier\AbstractCarrier");
        }
        return $carrier;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\Order\AddressConverter;
use Magento\Sales\Service\V1\Data\OrderAddress;

/**
 * Class OrderAddressUpdate
 * @package Magento\Sales\Service\V1
 */
class OrderAddressUpdate implements OrderAddressUpdateInterface
{
    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        AddressConverter $addressConverter
    ) {
        $this->addressConverter = $addressConverter;
    }

    /**
     * Invoke order address update service
     *
     * @param \Magento\Sales\Service\V1\Data\OrderAddress $orderAddress
     * @return bool
     */
    public function invoke(OrderAddress $orderAddress)
    {
        $orderAddressModel = $this->addressConverter->getModel($orderAddress);
        return (bool)$orderAddressModel->save();
    }
}

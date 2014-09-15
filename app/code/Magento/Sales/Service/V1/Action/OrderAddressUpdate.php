<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\AddressConverter;
use Magento\Sales\Service\V1\Data\OrderAddress;

/**
 * Class OrderAddressUpdate
 */
class OrderAddressUpdate
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
        $orderAddressModel->save();
        return true;
    }
}

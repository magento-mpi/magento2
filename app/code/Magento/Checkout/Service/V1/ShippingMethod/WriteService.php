<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart\ShippingMethod;


class WriteService implements WriteServiceInterface
{
    protected $addressFactory;

    public function __construct(
      \Magento\Sales\Model\Quote\AddressFactory $addressFactory
    ) {
        $this->addressFactory = $addressFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function method($carrierId, $methodId)
    {
    }

}
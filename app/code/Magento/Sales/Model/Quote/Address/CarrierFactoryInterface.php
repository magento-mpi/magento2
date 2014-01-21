<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Sales\Model\Quote\Address;

interface CarrierFactoryInterface
{
    /**
     * @return AbstractCarrierInterface
     */
    public function create();
}

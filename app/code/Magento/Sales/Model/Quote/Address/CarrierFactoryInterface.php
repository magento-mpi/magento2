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
     * Get carrier instance
     *
     * @param string $carrierCode
     * @return bool|AbstractCarrierInterface
     */
    public function get($carrierCode);

    /**
     * Create carrier instance
     *
     * @param string $carrierCode
     * @param int|null $storeId
     * @return bool|AbstractCarrierInterface
     */
    public function create($carrierCode, $storeId = null);

    /**
     * Get carrier by its code if it is active
     *
     * @param string $carrierCode
     * @return bool|AbstractCarrierInterface
     */
    public function getIfActive($carrierCode);

    /**
     * Create carrier by its code if it is active
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|AbstractCarrierInterface
     */
    public function createIfActive($carrierCode, $storeId = null);
}

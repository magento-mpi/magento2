<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

interface RmaWriteInterface
{
    /**
     * @param int $rmaId
     * @param string $trackNumber
     * @param string $carrierCode
     * @param string $carrierTitle
     *
     * @throws \Exception
     * @return bool
     */
    public function addTrack($rmaId, $trackNumber, $carrierCode = '', $carrierTitle = '');

    /**
     * @param int $rmaId
     * @param int $trackId
     * @return bool
     */
    public function removeTrackById($rmaId, $trackId);

    /**
     * Create rma
     *
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Rma\Service\V1\Data\Rma $rmaDataObject);

    /**
     * Update rma
     *
     * @param int $rmaId
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function update($rmaId, \Magento\Rma\Service\V1\Data\Rma $rmaDataObject);

    /**
     * Create shipping label for rma
     *
     * @param int $rmaId
     * @param array $packages
     * @param string $carrierCode
     * @param string $carrierTitle
     * @param string $methodTitle
     * @param null|float $price
     *
     * @throws \Exception
     * @return bool
     */
    public function createLabel($rmaId, $packages, $carrierCode = '', $carrierTitle = '', $methodTitle = '', $price = null);
}

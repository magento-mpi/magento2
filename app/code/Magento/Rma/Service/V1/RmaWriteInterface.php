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
}

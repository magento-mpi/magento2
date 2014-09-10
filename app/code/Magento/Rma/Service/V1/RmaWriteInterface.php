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
     * @param string|mixed $number
     * @param string|mixed $carrier
     * @param string $title
     *
     * @return bool
     */
    public function addTrack($rmaId, $number, $carrier, $title = '');

    /**
     * @param int $rmaId
     * @param int $trackId
     * @return bool
     */
    public function removeTrackById($rmaId, $trackId);
}

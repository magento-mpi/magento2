<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

interface TrackWriteInterface
{
    /**
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Track $track
     *
     * @throws \Exception
     * @return bool
     */
    public function addTrack($id, \Magento\Rma\Service\V1\Data\Track $track);

    /**
     * @param int $id
     * @param int $trackId
     * @return bool
     */
    public function removeTrackById($id, $trackId);
}

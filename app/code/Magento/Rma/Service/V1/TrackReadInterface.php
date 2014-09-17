<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

interface TrackReadInterface
{
    /**
     * Return list of track data objects based on search criteria
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Track[]
     */
    public function getTracks($id);

    /**
     * @param int $id
     *
     * @throws \Exception
     * @return string
     */
    public function getShippingLabelPdf($id);
}

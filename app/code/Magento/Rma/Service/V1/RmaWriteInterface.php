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
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function update($id, \Magento\Rma\Service\V1\Data\Rma $rmaDataObject);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\RmaRepository;

class RmaWrite implements RmaWriteInterface
{
    /**
     * @var \Magento\Rma\Model\Rma\Converter
     */
    private $converter;

    /**
     * @param \Magento\Rma\Model\Rma\Converter $converter
     */
    public function __construct(
        \Magento\Rma\Model\Rma\Converter $converter
    ) {
        $this->converter = $converter;
    }

    /**
     * Create rma
     *
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Rma\Service\V1\Data\Rma $rmaDataObject)
    {
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->createNewRmaModel($rmaDataObject, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }

    /**
     * Update rma
     *
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function update($id, \Magento\Rma\Service\V1\Data\Rma $rmaDataObject)
    {
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->getModel($id, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\Rma\Converter;
use Magento\Rma\Model\Rma\PermissionChecker;

class RmaWrite implements RmaWriteInterface
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param Converter $converter
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        Converter $converter,
        PermissionChecker $permissionChecker
    ) {
        $this->converter = $converter;
        $this->permissionChecker = $permissionChecker;
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
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();
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
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->getModel($id, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface CreditmemoGetInterface
 */
interface CreditmemoGetInterface
{
    /**
     * Invoke creditmemo get service
     *
     * @param int $id
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id);
}

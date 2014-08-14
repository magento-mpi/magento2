<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface CreditmemoCreateInterface
 *
 * @package Magento\Sales\Service\V1
 */
interface CreditmemoCreateInterface
{
    /**
     * @param \Magento\Sales\Service\V1\Data\Creditmemo $creditmemoDataObject
     * @return bool
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Creditmemo $creditmemoDataObject);
}

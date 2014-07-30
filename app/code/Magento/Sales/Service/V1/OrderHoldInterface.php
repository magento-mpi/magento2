<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface OrderHoldInterface
 */
interface OrderHoldInterface
{
    /**
     * Invoke orderHold service
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id);
}

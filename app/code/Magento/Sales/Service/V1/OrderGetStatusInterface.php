<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface OrderGetStatusInterface
 * @package Magento\Sales\Service\V1
 */
interface OrderGetStatusInterface
{
    /**
     * Retrieve order status by id
     *
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id);
}

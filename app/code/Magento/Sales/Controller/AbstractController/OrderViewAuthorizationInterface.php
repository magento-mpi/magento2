<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\AbstractController;

interface OrderViewAuthorizationInterface
{
    /**
     * Check if order can be viewed by user
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function canView(\Magento\Sales\Model\Order $order);
}

<?php
/**
 * Remove protector
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model\RemoveProtector;

class Disabled implements \Magento\Model\RemoveProtectorInterface
{
    /**
     * Safeguard function that checks if item can be removed
     *
     * @param \Magento\Model\AbstractModel $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canBeRemoved(\Magento\Model\AbstractModel $model)
    {
        return true;
    }
}

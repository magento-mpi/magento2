<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Backend\Model;

class RemoveProtector implements \Magento\Model\RemoveProtectorInterface
{
    /**
     * Safeguard function that checks if item can be removed
     * (on backend admin can remove all items)
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

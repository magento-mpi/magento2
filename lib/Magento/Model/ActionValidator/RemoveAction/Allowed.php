<?php
/**
 * Action validator for remove action
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model\ActionValidator\RemoveAction;

class Allowed extends \Magento\Model\ActionValidator\RemoveAction
{
    /**
     * Safeguard function that checks if item can be removed
     *
     * @param \Magento\Model\AbstractModel $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isAllowed(\Magento\Model\AbstractModel $model)
    {
        return true;
    }
}

<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model;

interface RemoveProtectorInterface
{

    /**
     * Safeguard function that checks if item can be removed
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function canBeRemoved(AbstractModel $model);
}

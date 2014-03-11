<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

/**
 * Dummy layout argument updater model
 */
class LayoutArgumentObjectUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param \Magento\Data\Collection $argument
     * @return \Magento\Data\Collection
     */
    public function update($argument)
    {
        $calls = $argument->getUpdaterCall();
        $calls[] = 'updater call';
        $argument->setUpdaterCall($calls);
        return $argument;
    }
}

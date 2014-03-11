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
class LayoutArgumentSimpleUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param int $argument
     * @return int
     */
    public function update($argument)
    {
        $argument++;
        return $argument;
    }
}

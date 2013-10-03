<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dummy layout argument updater model
 */
namespace Magento\Core\Model;

class LayoutArgumentSimpleUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
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

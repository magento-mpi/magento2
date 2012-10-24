<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dummy layout argument updater model
 */
class Mage_Core_Model_LayoutArgumentSimpleUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
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

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
class Magento_Core_Model_LayoutArgumentObjectUpdater implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param Magento_Data_Collection $argument
     * @return Magento_Data_Collection
     */
    public function update($argument)
    {
        $calls = $argument->getUpdaterCall();
        $calls[] = 'updater call';
        $argument->setUpdaterCall($calls);
        return $argument;
    }
}

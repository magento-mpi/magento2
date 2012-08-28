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
class Mage_Core_Model_LayoutArgumentObjectUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param Mage_Core_Block_Text $argument
     * @return Mage_Core_Block_Text
     */
    public function update($argument)
    {
        $calls = $argument->getUdaterCall();
        if (true == empty($calls)) {
            $calls = array();
        }
        $calls[] = 'updater call';
        $argument->setUdaterCall($calls);
        return $argument;
    }
}

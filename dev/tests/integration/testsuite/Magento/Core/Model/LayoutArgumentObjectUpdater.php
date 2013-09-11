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
class Magento_Core_Model_LayoutArgumentObjectUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param \Magento\Core\Block\Text $argument
     * @return \Magento\Core\Block\Text
     */
    public function update($argument)
    {
        $calls = $argument->getUpdaterCall();
        if (true == empty($calls)) {
            $calls = array();
        }
        $calls[] = 'updater call';
        $argument->setUpdaterCall($calls);
        return $argument;
    }
}

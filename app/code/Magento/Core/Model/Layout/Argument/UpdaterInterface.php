<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout object argument updater interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument);
}

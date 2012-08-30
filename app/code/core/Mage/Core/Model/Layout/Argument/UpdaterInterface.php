<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout object argument updater interface
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument);
}

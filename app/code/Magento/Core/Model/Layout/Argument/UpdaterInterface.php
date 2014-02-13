<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Layout\Argument;

/**
 * Layout object argument updater interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

interface UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument);
}

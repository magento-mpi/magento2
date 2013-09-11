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
 * Argument handler factory interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument;

interface HandlerFactoryInterface
{
    /**
     * Create concrete handler object
     * @return \Magento\Core\Model\Layout\Argument\HandlerInterface
     */
    public function createHandler();
}

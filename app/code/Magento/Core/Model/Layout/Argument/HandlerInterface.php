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
 * Layout object argument interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument;

interface HandlerInterface
{
    /**
     * Parse specified argument node
     *
     * @param \Magento\View\Layout\Element $argument
     * @return array
     */
    public function parse(\Magento\View\Layout\Element $argument);

    /**
     * Process argument value
     *
     * @param array $argument
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function process(array $argument);
}

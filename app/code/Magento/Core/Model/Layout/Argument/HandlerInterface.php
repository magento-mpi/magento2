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
interface Magento_Core_Model_Layout_Argument_HandlerInterface
{
    /**
     * Process argument value
     * @param array $value
     * @return mixed
     */
    public function process($value);

    /**
     * Parse given argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @throws InvalidArgumentException
     * @return array
     */
    public function parse(Magento_Core_Model_Layout_Element $argument);
}

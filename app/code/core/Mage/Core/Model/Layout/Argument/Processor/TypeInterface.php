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
 * Layout object argument interface
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Core_Model_Layout_Argument_Processor_TypeInterface
{
    /**
     * Process argument value
     * @param $value
     * @return mixed
     */
    public function process($value);
}

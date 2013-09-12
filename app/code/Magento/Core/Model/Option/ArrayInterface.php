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
 * Option array interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray();
}

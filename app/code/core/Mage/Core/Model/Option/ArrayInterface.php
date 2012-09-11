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
 * Option array interface
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray();
}

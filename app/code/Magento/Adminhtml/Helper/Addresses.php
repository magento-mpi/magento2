<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml addresses helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Helper;

class Addresses extends \Magento\Core\Helper\AbstractHelper
{
    const DEFAULT_STREET_LINES_COUNT = 2;

    /**
     * Check if number of street lines is non-zero
     *
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return \Magento\Customer\Model\Attribute
     */
    public function processStreetAttribute(\Magento\Customer\Model\Attribute $attribute)
    {
        if($attribute->getScopeMultilineCount() <= 0) {
            $attribute->setScopeMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }
}

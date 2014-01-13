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
namespace Magento\Backend\Helper;

class Addresses extends \Magento\App\Helper\AbstractHelper
{
    const DEFAULT_STREET_LINES_COUNT = 2;

    /**
     * Check if number of street lines is non-zero
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     * @return \Magento\Customer\Model\Attribute
     */
    public function processStreetAttributeFromDTO(\Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute)
    {
        if ($attribute->getMultilineCount() <= 0) {
            $attribute->getMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }

    /**
     * Check if number of street lines is non-zero
     *
     * @deprecated This and the dependent callers should be refactored to use DTO. Temporarily created
     * \Magento\Backend\Helper\Addresses::processStreetAttributeFromDTO
     * @see \Magento\Bundle\Model\Product\Price::getSelectionFinalTotalPrice()
     *
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return \Magento\Customer\Model\Attribute
     */
    public function processStreetAttribute(\Magento\Customer\Model\Attribute $attribute)
    {
        if ($attribute->getScopeMultilineCount() <= 0) {
            $attribute->setScopeMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }
}

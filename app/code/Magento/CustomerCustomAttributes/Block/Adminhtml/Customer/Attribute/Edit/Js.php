<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer and Customer Address Attributes Edit JavaScript Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Attribute\Edit;

class Js
    extends \Magento\Adminhtml\Block\Template
{
    /**
     * Retrieve allowed Input Validate Filters in JSON format
     *
     * @return string
     */
    public function getValidateFiltersJson()
    {
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode
            (\Mage::helper('Magento\CustomerCustomAttributes\Helper\Data')->getAttributeValidateFilters()
        );
    }

    /**
     * Retrieve allowed Input Filter Types in JSON format
     *
     * @return string
     */
    public function getFilteTypesJson()
    {
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(
            \Mage::helper('Magento\CustomerCustomAttributes\Helper\Data')->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return \Mage::helper('Magento\CustomerCustomAttributes\Helper\Data')->getAttributeInputTypes();
    }
}

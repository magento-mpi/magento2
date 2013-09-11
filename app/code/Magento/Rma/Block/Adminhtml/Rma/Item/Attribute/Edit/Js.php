<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Items Attributes Edit JavaScript Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit;

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
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(
            \Mage::helper('Magento\Rma\Helper\Eav')->getAttributeValidateFilters()
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
            \Mage::helper('Magento\Rma\Helper\Eav')->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return \Mage::helper('Magento\Rma\Helper\Eav')->getAttributeInputTypes();
    }
}

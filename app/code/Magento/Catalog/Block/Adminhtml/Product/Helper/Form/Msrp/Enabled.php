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
 * Product form MAP Enabled field helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Msrp;

class Enabled extends \Magento\Framework\Data\Form\Element\Select
{
    /**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (is_null($this->getValue())) {
            $this->setValue(\Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG);
        }
        return parent::getElementHtml();
    }
}

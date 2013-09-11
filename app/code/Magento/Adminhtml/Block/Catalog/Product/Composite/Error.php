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
 * Adminhtml block for showing product options fieldsets
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author    Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Composite;

class Error extends \Magento\Core\Block\Template
{
    /**
     * Returns error message to show what kind of error happened during retrieving of product
     * configuration controls
     *
     * @return string
     */
    public function _toHtml()
    {
        $message = \Mage::registry('composite_configure_result_error_message');
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(array('error' => true, 'message' => $message));
    }
}
